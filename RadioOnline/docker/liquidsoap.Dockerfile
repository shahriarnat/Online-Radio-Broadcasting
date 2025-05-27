FROM savonet/liquidsoap:v2.3.1

# Switch to root to install packages
USER root

# Ensure apt works and install curl
RUN mkdir -p /var/lib/apt/lists/partial && \
    apt-get update && \
    apt-get install -y curl && \
    rm -rf /var/lib/apt/lists/*

# Switch back to liquidsoap user
USER liquidsoap
