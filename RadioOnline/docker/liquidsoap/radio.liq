#!/usr/bin/liquidsoap

# Allow running as root
settings.init.allow_root := true
settings.log.level := 3

# Enable telnet for debugging
settings.server.telnet := true
settings.server.telnet.port := 1234

# Force start Liquidsoap
settings.init.force_start := true

# Frame size for lower latency
set("frame.audio.size", 512)

# Live input
live = mksafe(input.http("http://icecast:1990/live"))

# Function to fetch a track URL from the backend API
def fetch_track(channel_id)
  url = process.read("curl -s -A 'RadioLiquidSoap/1.0' http://backend:8000/track.pls?channel_id=#{channel_id}")
  log("*** Channel #{channel_id} track URL: #{url}")
  request.create(temporary=true, url)
end

# Dynamic source that fetches a new track only when the current one ends
def dynamic_track(channel_id)
  # Create a source that fetches one track at a time
  s = request.dynamic(fun () -> fetch_track(channel_id))
  # Trigger a new fetch when the track ends
  s = on_end(fun () -> ignore(fetch_track(channel_id)), s)
  s
end

# Create sources for each channel
ch1 = dynamic_track(1)
ch2 = dynamic_track(2)

# Stream 1: Fallback to live if ch1 is not available
stream1 = amplify(1.5, fallback([ch1, live]))

# Stream 2: Play ch2, fallback to blank
stream2 = amplify(1.5, fallback([ch2, blank()]))

# Output Stream 1
output.icecast(
  %mp3(bitrate=128, samplerate=44100),
  stream1,
  host="icecast",
  port=1990,
  password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount="/stream1",
  buffer=0.1,
  max_buffer=0.5
)

# Output Stream 2
output.icecast(
  %mp3(bitrate=128, samplerate=44100),
  stream2,
  host="icecast",
  port=1990,
  password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount="/stream2",
  buffer=0.1,
  max_buffer=0.5
)
