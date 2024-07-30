import sys
from langchain_community.embeddings.sentence_transformer import SentenceTransformerEmbeddings
from langchain_community.vectorstores import Chroma
from langchain.chains import create_retrieval_chain
from langchain.chains.combine_documents import create_stuff_documents_chain
from langchain_core.prompts import ChatPromptTemplate
from langchain_openai import OpenAI
from langchain.globals import set_llm_cache
from langchain.cache import InMemoryCache
def chat(input_text):
    embedding_function = SentenceTransformerEmbeddings(model_name="all-MiniLM-L6-v2")
    db = Chroma(persist_directory="C:/Users/waled/Desktop/chamwings/EmployeeChatBot/vectorstore", embedding_function=embedding_function)
    retriever = db.as_retriever(k=2)

    llm = OpenAI(base_url="http://localhost:1234/v1", api_key="not-needed", n=2, best_of=2)
    set_llm_cache(InMemoryCache())
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
    rag_chain = create_retrieval_chain(retriever, question_answer_chain)

    #json
    # for chunk in rag_chain.stream({"input":input_text}):
    #     print(chunk)

    #answer
    # for chunk in rag_chain.stream({"input":input_text}):
    #     if answer_chunk := chunk.get("answer"):
    #         print(f"{answer_chunk}|", end="")

    chain = rag_chain.pick("answer")

    for chunk in chain.stream({"input": input_text}):
        print(f"{chunk}|", end="")


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python chat_script.py '<input_text>'")
        sys.exit(1)

    input_text = sys.argv[1]
    chat(input_text)