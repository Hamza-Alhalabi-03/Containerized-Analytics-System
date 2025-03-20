// This script creates the analytics database and user if they don't exist
db = db.getSiblingDB('analytics_data');

db.createUser({
  user: 'analytics_user',
  pwd: 'analytics_password',
  roles: [
    { role: 'readWrite', db: 'analytics_data' }
  ]
});

// Create collections with validation if needed
db.createCollection('analytics_results');