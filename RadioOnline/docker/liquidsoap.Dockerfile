FROM savonet/liquidsoap:v2.3.1

# Switch to root to install packages
USER root

# Ensure apt works and install curl
RUN mkdir -p /var/lib/apt/lists/partial && \
    apt-get update && \
    apt-get install -y vim curl cron && \
    rm -rf /var/lib/apt/lists/*

# Create a cron job
RUN echo "* * * * * find /tmp -name 'liq-*' -mmin +1440 -delete" > /etc/cron.d/liquidsoap.cron

# Switch back to liquidsoap user
USER liquidsoap
