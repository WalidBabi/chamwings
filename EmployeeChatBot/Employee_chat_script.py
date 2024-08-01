import sys
import os
import json
from langchain_community.embeddings.sentence_transformer import SentenceTransformerEmbeddings
from langchain_community.vectorstores import Chroma
from langchain.chains import create_retrieval_chain
from langchain.chains.combine_documents import create_stuff_documents_chain
from langchain_core.prompts import ChatPromptTemplate
from langchain_openai import OpenAI
from langchain.retrievers import MultiQueryRetriever
from langchain.retrievers import EnsembleRetriever

def chat(input_text):
    embedding_function = SentenceTransformerEmbeddings(model_name="all-MiniLM-L6-v2")

    vectorstore_base_dir = "C:/Users/waled/Desktop/chamwings/EmployeeChatBot/vectorstore"
    subdirectories = [os.path.join(vectorstore_base_dir, d) for d in os.listdir(vectorstore_base_dir) if os.path.isdir(os.path.join(vectorstore_base_dir, d))]
 
    retrievers = []

    for subdirectory in subdirectories:
        db = Chroma(persist_directory=subdirectory, embedding_function=embedding_function)
        retrievers.append(db.as_retriever(search_kwargs={"k": 2}))
    
    # Combine retrievers using EnsembleRetriever
    combined_retriever = EnsembleRetriever(retrievers=retrievers, weights=[1/len(retrievers)] * len(retrievers))
  
    llm = OpenAI(base_url="http://localhost:1234/v1", api_key="not-needed", n=2, best_of=2)
    
    system_prompt = (
        "You are an assistant for question-answering tasks. "
        "Use the following pieces of retrieved context to answer "
        "the question. If you don't know the answer, say that you "
        "don't know. Use three sentences maximum and keep the "
        "answer concise."
        "\n\n"
        "{context}"
    )

    prompt = ChatPromptTemplate.from_messages(
        [
            ("system", system_prompt),
            ("human", "{input}"),
        ]
    )

    question_answer_chain = create_stuff_documents_chain(llm, prompt)
    rag_chain = create_retrieval_chain(combined_retriever, question_answer_chain)

    chain = rag_chain.pick("answer")

    response = ""
    #json
    for chunk in rag_chain.stream({"input":input_text}):
        print(chunk)
    # for chunk in chain.stream({"input": input_text}):
    #     response += chunk

    return response

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python chat_script.py '<input_text>'")
        sys.exit(1)

    input_text = sys.argv[1]
    response = chat(input_text)
    
    # Create a JSON object with the input and response
    # output = {
    #     "input": input_text,
    #     "response": response
    # }
    
    # # Print the JSON-formatted string
    # print(json.dumps(output, ensure_ascii=False))