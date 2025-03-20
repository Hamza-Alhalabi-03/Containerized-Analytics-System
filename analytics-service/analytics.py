import mysql.connector
import pymongo
import time
from datetime import datetime

def get_mysql_connection():
    print("Attempting to connect to MySQL...")
    try:
        conn = mysql.connector.connect(
            host="mysql",
            user="devuser",
            password="devpassword",
            database="dev_analytics"
        )
        print("Successfully connected to MySQL")
        return conn
    except Exception as e:
        print(f"Error connecting to MySQL: {str(e)}")
        raise

def get_mongodb_connection():
    print("Attempting to connect to MongoDB...")
    try:
        client = pymongo.MongoClient("mongodb://analytics_user:analytics_password@mongodb:27017/analytics_data")
        # Test the connection
        client.server_info()
        print("Successfully connected to MongoDB")
        return client
    except Exception as e:
        print(f"Error connecting to MongoDB: {str(e)}")
        raise

def calculate_statistics():
    print("Starting statistics calculation...")
    try:
        mysql_conn = get_mysql_connection()
        mongodb_conn = get_mongodb_connection()
        
        try:
            # Get MySQL cursor
            mysql_cursor = mysql_conn.cursor(dictionary=True)
            
            print("Executing MySQL query...")
            # Calculate statistics
            mysql_cursor.execute("""
                SELECT 
                    COUNT(*) as total_records,
                    COUNT(DISTINCT developer_name) as total_developers,
                    AVG(hours_per_week) as avg_working_hours,
                    MAX(hours_per_week) as max_working_hours,
                    MIN(hours_per_week) as min_working_hours,
                    AVG(years_experience) as avg_experience
                FROM developer_data
            """)
            
            stats = mysql_cursor.fetchone()
            print("Successfully retrieved statistics from MySQL")
            
            # Add timestamp
            stats['timestamp'] = datetime.now().isoformat()
            
            # Store in MongoDB
            print("Storing results in MongoDB...")
            mongodb_db = mongodb_conn['analytics_data']
            mongodb_collection = mongodb_db['analytics_results']
            mongodb_collection.insert_one(stats)
            
            print(f"Statistics calculated and stored at {stats['timestamp']}")
            print("Statistics:", stats)
            
        except Exception as e:
            print(f"Error during statistics calculation: {str(e)}")
            raise
        finally:
            mysql_cursor.close()
            mysql_conn.close()
            mongodb_conn.close()
            
    except Exception as e:
        print(f"Fatal error in calculate_statistics: {str(e)}")
        # Wait for 30 seconds before retrying in case of connection issues
        time.sleep(30)

def main():
    print("Analytics service starting...")
    while True:
        try:
            calculate_statistics()
        except Exception as e:
            print(f"Error in main loop: {str(e)}")
        # Wait for 10 minutes
        print("Waiting 10 minutes before next calculation...")
        time.sleep(600)

if __name__ == "__main__":
    main() 