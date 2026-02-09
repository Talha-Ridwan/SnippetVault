# Use Node.js 18 on Alpine Linux (Lightweight)
FROM node:22-alpine

# Set the working directory inside the container
WORKDIR /app

# Copy package definition files first (for better caching)
COPY package.json package-lock.json ./

# Install dependencies
# We use 'ci' (Clean Install) for more reliable builds, or 'install' for dev
RUN npm install

# Copy the rest of your app's source code
COPY . .

# Expose Port 3000 (Default for Next.js)
EXPOSE 3000

# Start the development server with hot-reloading
CMD ["npm", "run", "dev"]
