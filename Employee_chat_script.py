from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import os
from dotenv import load_dotenv
# from langchain_community.embeddings.sentence_transformer import SentenceTransformerEmbeddings
from langchain_huggingface import HuggingFaceEmbeddings
from langchain_community.vectorstores import Chroma
from langchain.chains import create_retrieval_chain
from langchain.chains.combine_documents import create_stuff_documents_chain
from langchain_core.prompts import ChatPromptTemplate
from langchain_openai import OpenAI
from langchain.retrievers import EnsembleRetriever

# Load environment variables
load_dotenv()

app = FastAPI()

class ChatRequest(BaseModel):
    input_text: str

def chat(input_text: str) -> str:
    try:
        embedding_function = HuggingFaceEmbeddings(model_name="all-MiniLM-L6-v2")

        # Retrieve vector store base directory from environment variable
        vectorstore_base_dir = os.getenv("VECTORSTORE_BASE_DIR")
        if not vectorstore_base_dir:
            raise ValueError("VECTORSTORE_BASE_DIR is not set in the .env file")
        
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
        for chunk in rag_chain.stream({"input": input_text}):
            response += chunk

        return response

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/chat")
async def chat_endpoint(request: ChatRequest):
    response = chat(request.input_text)
    return {"answer": response}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)


#uvicorn Employee_chat_script:app --reload
