import sys
import os
import traceback
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()
def print_environment():
    print("Python version:", sys.version)
    print("Python path:", sys.executable)
    print("Working directory:", os.getcwd())
    print("Script location:", os.path.abspath(__file__))
    print("Arguments:", sys.argv)

def ingest_pdf(file_path):
    try:
        from langchain_community.document_loaders import PyPDFLoader
        from langchain.text_splitter import RecursiveCharacterTextSplitter
        from langchain_community.vectorstores import Chroma
        from langchain_community.embeddings.sentence_transformer import SentenceTransformerEmbeddings
    except ImportError as e:
        print(f"Error: Missing required libraries: {e}")
        sys.exit(1)

    print(f"Processing file: {file_path}")

    pdf_name = os.path.splitext(os.path.basename(file_path))[0]
    
    persist_directory_base = os.getenv("PERSIST_DIRECTORY")

    if not persist_directory_base:
        print("Error: PERSIST_DIRECTORY is not set in the .env file")
        sys.exit(1)

    persist_directory = os.path.join(persist_directory_base, pdf_name)
    try:
        loader = PyPDFLoader(file_path)
        docs = loader.load()
    except Exception as e:
        print(f"Error loading PDF: {e}")
        print("Traceback:")
        print(traceback.format_exc())
        sys.exit(1)

    try:
        text_splitter = RecursiveCharacterTextSplitter(chunk_size=1000, chunk_overlap=200)
        splits = text_splitter.split_documents(docs)
    except Exception as e:
        print(f"Error splitting text: {e}")
        print("Traceback:")
        print(traceback.format_exc())
        sys.exit(1)

    try:
        embedding_function = SentenceTransformerEmbeddings(model_name="all-MiniLM-L6-v2")
        vectorstore = Chroma.from_documents(documents=splits, embedding=embedding_function, persist_directory=persist_directory)
        # vectorstore.persist()
    except Exception as e:
        print(f"Error creating vectorstore: {e}")
        print("Traceback:")
        print(traceback.format_exc())
        sys.exit(1)

    print("PDF ingested successfully")

if __name__ == "__main__":
    print_environment()

    if len(sys.argv) != 2:
        print("Usage: python ingest_pdf_script.py <pdf_file_path>")
        sys.exit(1)

    pdf_path = sys.argv[1]

    if not os.path.exists(pdf_path):
        print(f"Error: File not found: {pdf_path}")
        sys.exit(1)

    try:
        ingest_pdf(pdf_path)
    except Exception as e:
        print(f"An error occurred: {str(e)}")
        print("Traceback:")
        print(traceback.format_exc())
        sys.exit(1)
