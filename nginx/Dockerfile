# Use the official lightweight Nginx image
FROM nginx:alpine

# Remove the default Nginx configuration
RUN rm /etc/nginx/conf.d/default.conf

# Copy our custom configuration file into the container
COPY default.conf /etc/nginx/conf.d/

