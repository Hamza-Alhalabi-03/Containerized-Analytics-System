FROM mongo:latest

# Set default values for MongoDB configuration
ENV MONGO_INITDB_ROOT_USERNAME=admin
ENV MONGO_INITDB_ROOT_PASSWORD=adminpassword
ENV MONGO_INITDB_DATABASE=analytics_data

# You can add custom MongoDB configurations here
COPY mongod.conf /etc/mongod.conf

# If you need initialization scripts
COPY ./init-mongo.js /docker-entrypoint-initdb.d/

EXPOSE 27017

CMD ["mongod"]