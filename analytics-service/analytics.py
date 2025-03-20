import mysql.connector
import pymongo
import time
from datetime import datetime
import sys
from decimal import Decimal

# Enable more detailed logging
print("Starting analytics service...", flush=True)

def convert_decimal_to_float(obj):
    if isinstance(obj, Decimal):
        return float(obj)
    return obj

def get_mysql_connection():
    print("Attempting to connect to MySQL...", flush=True)
    try:
        conn = mysql.connector.connect(
            host="mysql",
            user="devuser",
            password="devpassword",
            database="dev_analytics"
        )
        print("Successfully connected to MySQL", flush=True)
        return conn
    except Exception as e:
        print(f"Error connecting to MySQL: {str(e)}", flush=True)
        print(f"Error type: {type(e)}", flush=True)
        raise

def get_mongodb_connection():
    print("Attempting to connect to MongoDB...", flush=True)
    max_retries = 5
    retry_delay = 5  # seconds
    
    for attempt in range(max_retries):
        try:
            # Using the MongoDB container name as hostname
            client = pymongo.MongoClient(
                "mongodb://analytics_user:analytics_password@mongodb:27017/analytics_data?authSource=analytics_data",
                serverSelectionTimeoutMS=5000  # 5 second timeout
            )
            # Test the connection
            client.server_info()
            print("Successfully connected to MongoDB", flush=True)
            return client
        except Exception as e:
            print(f"Error connecting to MongoDB (attempt {attempt + 1}/{max_retries}): {str(e)}", flush=True)
            if attempt < max_retries - 1:
                print(f"Retrying in {retry_delay} seconds...", flush=True)
                time.sleep(retry_delay)
            else:
                raise

def calculate_statistics():
    print("Starting statistics calculation...", flush=True)
    try:
        mysql_conn = get_mysql_connection()
        mongodb_conn = get_mongodb_connection()
        
        try:
            # Get MySQL cursor
            mysql_cursor = mysql_conn.cursor(dictionary=True)
            
            print("Executing MySQL query...", flush=True)
            # Calculate statistics with explicit CAST to DOUBLE
            mysql_cursor.execute("""
                SELECT 
                    CAST(COUNT(*) AS UNSIGNED) as total_records,
                    CAST(COUNT(DISTINCT developer_name) AS UNSIGNED) as total_developers,
                    CAST(AVG(CAST(hours_per_week AS DOUBLE)) AS DOUBLE) as avg_working_hours,
                    CAST(MAX(CAST(hours_per_week AS DOUBLE)) AS DOUBLE) as max_working_hours,
                    CAST(MIN(CAST(hours_per_week AS DOUBLE)) AS DOUBLE) as min_working_hours,
                    CAST(AVG(CAST(years_experience AS DOUBLE)) AS DOUBLE) as avg_experience
                FROM developer_data
            """)
            
            stats = mysql_cursor.fetchone()
            print(f"Successfully retrieved statistics from MySQL: {stats}", flush=True)
            
            if not stats or stats['total_records'] == 0:
                print("No data found in MySQL database", flush=True)
                return
            
            # Convert all numeric values to Python float
            stats = {k: float(v) if isinstance(v, (int, Decimal)) else v for k, v in stats.items()}
            
            # Add timestamp
            stats['timestamp'] = datetime.now().isoformat()
            
            # Store in MongoDB
            print("Storing results in MongoDB...", flush=True)
            mongodb_db = mongodb_conn['analytics_data']
            mongodb_collection = mongodb_db['analytics_results']
            
            # Print the exact document being inserted for debugging
            print(f"Document to be inserted: {stats}", flush=True)
            
            result = mongodb_collection.insert_one(stats)
            print(f"Successfully inserted document with ID: {result.inserted_id}", flush=True)
            
            print(f"Statistics calculated and stored at {stats['timestamp']}", flush=True)
            print("Statistics:", stats, flush=True)
            
        except Exception as e:
            print(f"Error during statistics calculation: {str(e)}", flush=True)
            print(f"Error type: {type(e)}", flush=True)
            raise
        finally:
            mysql_cursor.close()
            mysql_conn.close()
            mongodb_conn.close()
            
    except Exception as e:
        print(f"Fatal error in calculate_statistics: {str(e)}", flush=True)
        print(f"Error type: {type(e)}", flush=True)
        # Wait for 30 seconds before retrying in case of connection issues
        time.sleep(30)

def main():
    print("Analytics service starting...", flush=True)
    while True:
        try:
            calculate_statistics()
        except Exception as e:
            print(f"Error in main loop: {str(e)}", flush=True)
            print(f"Error type: {type(e)}", flush=True)
        # Wait for 10 minutes
        print("Waiting one minute before next calculation...", flush=True)
        time.sleep(60)

if __name__ == "__main__":
    try:
        main()
    except Exception as e:
        print(f"Fatal error in main: {str(e)}", flush=True)
        print(f"Error type: {type(e)}", flush=True)
        sys.exit(1) 