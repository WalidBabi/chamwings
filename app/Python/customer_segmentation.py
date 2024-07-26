import logging
import json
import sys
from typing import Dict, Any
import pandas as pd
import numpy as np
from sklearn.preprocessing import StandardScaler
from sklearn.cluster import KMeans
from sqlalchemy import create_engine

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def get_db_connection():
    try:
        # Replace with your actual database URL
        db_url = "mysql://root:@localhost/chamwings"
        engine = create_engine(db_url)
        return engine
    except Exception as e:
        logger.error(f"Error connecting to database: {e}")
        return None

def perform_segmentation() -> Dict[str, Any]:
    try:
        engine = get_db_connection()
        if not engine:
            return {'error': 'Failed to connect to database'}

        query = "SELECT * FROM customer_segmentation"
        data = pd.read_sql(query, engine)

        logger.info(f'Fetched data count: {len(data)}')

        if data.empty:
            logger.warning('No data found in customer_segmentation view')
            return {'error': 'No data available for segmentation'}

        # Prepare data for clustering
        features = ['age', 'total_reservations', 'avg_ticket_price', 'total_flights']
        samples = data[features].fillna(0).values

        # Normalize features
        scaler = StandardScaler()
        normalized_samples = scaler.fit_transform(samples)

        # Perform K-means clustering
        kmeans = KMeans(n_clusters=3, random_state=42)
        clusters = kmeans.fit_predict(normalized_samples)

        # Add cluster labels to the data
        data['cluster'] = clusters

        # Analyze segments
        analysis = {}
        for cluster in range(3):
            cluster_data = data[data['cluster'] == cluster]
            analysis[f'cluster_{cluster}'] = {
                'size': int(len(cluster_data)),
                'avg_age': float(cluster_data['age'].mean()),
                'avg_reservations': float(cluster_data['total_reservations'].mean()),
                'avg_ticket_price': float(cluster_data['avg_ticket_price'].mean()) if not np.isnan(cluster_data['avg_ticket_price'].mean()) else None,
                'avg_total_flights': float(cluster_data['total_flights'].mean()),
                'top_countries': cluster_data['country_of_residence'].value_counts().nlargest(5).to_dict()
            }

        return {'success': 'Segmentation completed', 'results': analysis}

    except Exception as e:
        logger.error(f"An error occurred during segmentation: {str(e)}")
        return {'error': f'An error occurred during segmentation: {str(e)}'}

if __name__ == "__main__":
    result = perform_segmentation()
    print(json.dumps(result, default=lambda x: None if isinstance(x, float) and np.isnan(x) else x))
    sys.stdout.flush()