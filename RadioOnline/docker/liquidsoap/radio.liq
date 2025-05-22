# Load 2 remote streams
music = input.http("http://icecast:1990/music.mp3")
live = input.http("http://icecast:1990/live.mp3")

# Mix both streams together
live_music_mix = add([music, live])

# Queue for uploaded tracks
queue_raw = request.queue(id="main")

# Crossfade queue + safe fallback to silence if it fails
queue = fallback(track_sensitive=false, [
  crossfade(start_next=3.0, fade_out=2.0, fade_in=2.0, queue_raw),
  blank()
])

# Main priority: queue first, fallback to live+music stream
radio = fallback(track_sensitive=false, [queue, live_music_mix])

# Output to Icecast
output.icecast(
  %mp3,
  host = "icecast",
  port = 2990,
  password = "source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount = "/music.mp3",
  radio
)

# Enable HTTP and Telnet APIs
server.telnet(port=1234, password="mysecret")
server.http(port=80)
