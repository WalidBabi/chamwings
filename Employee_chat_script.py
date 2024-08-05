from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import os
from dotenv import load_dotenv
from langchain_huggingface import HuggingFaceEmbeddings
from langchain_chroma import Chroma
from langchain_core.prompts import ChatPromptTemplate
from langchain_openai import ChatOpenAI
from langchain.retrievers import EnsembleRetriever
from langchain_core.messages import AIMessage, HumanMessage, SystemMessage
import logging

# Load environment variables
load_dotenv()

app = FastAPI()

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Global variables
global_retriever = None
chat_histories = {}

def initialize_retriever():
    global global_retriever
    embedding_function = HuggingFaceEmbeddings(model_name="all-MiniLM-L6-v2")

    vectorstore_base_dir = os.getenv("VECTORSTORE_BASE_DIR")
    if not vectorstore_base_dir:
        raise ValueError("VECTORSTORE_BASE_DIR is not set in the .env file")
    
    subdirectories = [os.path.join(vectorstore_base_dir, d) for d in os.listdir(vectorstore_base_dir) if os.path.isdir(os.path.join(vectorstore_base_dir, d))]
    retrievers = []

    for subdirectory in subdirectories:
        db = Chroma(persist_directory=subdirectory, embedding_function=embedding_function)
        retrievers.append(db.as_retriever(search_kwargs={"k": 2}))

    global_retriever = EnsembleRetriever(retrievers=retrievers, weights=[1/len(retrievers)] * len(retrievers))

@app.on_event("startup")
async def startup_event():
    initialize_retriever()

def get_chat_history(user_id: str, thread_id: str):
    if user_id not in chat_histories:
        chat_histories[user_id] = {}
    if thread_id not in chat_histories[user_id]:
        chat_histories[user_id][thread_id] = []
    return chat_histories[user_id][thread_id]

def chat(input_text: str, user_id: str, thread_id: str) -> str:
    try:
        llm = ChatOpenAI(base_url="http://localhost:1234/v1", api_key="not-needed")
        
        chat_history = get_chat_history(user_id, thread_id)

        # Log chat history for debugging
        logger.info(f"Chat history for user_id={user_id}, thread_id={thread_id}: {chat_history}")

        # Contextualize question
        contextualize_q_system_prompt = (
            "Given a chat history and the latest user question "
            "which might reference context in the chat history, "
            "formulate a standalone question which can be understood "
            "without the chat history. Do NOT answer the question, "
            "just reformulate it if needed and otherwise return it as is."
        )
        contextualize_messages = [
            SystemMessage(content=contextualize_q_system_prompt),
            HumanMessage(content=f"Chat history:\n{chat_history}\n\nLatest question: {input_text}")
        ]
        
        contextualized_question = llm.invoke(contextualize_messages).content

        # Log contextualized question for debugging
        logger.info(f"Contextualized question: {contextualized_question}")

        # Retrieve relevant documents
        docs = global_retriever.invoke(contextualized_question)
        context = "\n".join([doc.page_content for doc in docs])

        # Answer question
        system_prompt = (
            "Your name is CHAMAI. You are an assistant for question-answering tasks for Cham Wings Airlines. "
            "Use the following pieces of retrieved context to answer "
            "the question. If you don't know the answer, say that you "
            "don't know. Use three sentences maximum and keep the "
            "answer concise."
        )
        qa_messages = [
            SystemMessage(content=f"{system_prompt}\n\nContext: {context}"),
            HumanMessage(content=f"Chat history:\n{chat_history}\n\nQuestion: {input_text}")
        ]

        response = llm.invoke(qa_messages)

        # Update chat history
        chat_history.append(HumanMessage(content=input_text))
        chat_history.append(AIMessage(content=response.content))

        return response.content

    except Exception as e:
        logger.error(f"Error processing chat: {e}")
        raise HTTPException(status_code=500, detail=str(e))
        
class ChatRequest(BaseModel):
    input_text: str
    user_id: str
    thread_id: str  # Add thread_id to track different threads

@app.post("/chat")
async def chat_endpoint(request: ChatRequest):
    response = chat(request.input_text, request.user_id, request.thread_id)
    return {"answer": response}

@app.get("/chathistory/{user_id}/{thread_id}")
async def get_chat_history_endpoint(user_id: str, thread_id: str):
    history = get_chat_history(user_id, thread_id)
    return {"user_id": user_id, "thread_id": thread_id, "chat_history": [msg.content for msg in history]}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("Employee_chat_script:app", host="0.0.0.0", port=8001, reload=True)


#uvicorn Employee_chat_script:app --host 0.0.0.0 --port 8001 --reload