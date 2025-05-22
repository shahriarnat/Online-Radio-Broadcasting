# Load 2 remote streams
music = input.http("http://icecast:1990/music.mp3")
live = input.http("http://icecast:1990/live.mp3")

# Mix both streams together
live_music_mix = add([music, live])

# Queue for uploaded tracks
queue = request.queue(id="main")

# Optional: apply crossfade to mixed stream (if you're switching content)
queue = crossfade(start_next=3.0, fade_out=2.0, fade_in=2.0, queue_raw)

# When queue has tracks, play it. Else fallback to live streams
radio = fallback(track_sensitive=false, [queue, live_music_mix])

# Output to Icecast
output.icecast(
  %mp3,
  host = "icecast",
  port = 2990,
  password = "source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount = "/music.mp3",
  mixed
)

# Enable HTTP and Telnet APIs
server.telnet(port=1234, password="mysecret")
server.http(port=80)
