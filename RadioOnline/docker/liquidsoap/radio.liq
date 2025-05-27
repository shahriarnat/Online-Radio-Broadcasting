#!/usr/bin/liquidsoap

settings.init.allow_root := true
settings.log.level := 1

settings.server.telnet := false
settings.server.telnet.port := 1234

settings.crossfade.assume_autocue := true

settings.autocue.preferred := "internal"

settings.init.force_start := true

# Inputs
music = mksafe(input.http("http://127.0.0.1:1990/music.mp3"))
live  = mksafe(input.http("http://127.0.0.1:1990/live.mp3"))
#playlist = mksafe(playlist("https://dash.livemst.com/track.pls"))

# Function to get the next track URL from your API
def fetch_next_track()
  url = get_process_output("curl -s https://dash.livemst.com/track.pls")
  log("Next track URL: #{url}")
  request.create(url)
end

# Request source using dynamic API
playlist = request.dynamic(fetch_next_track)

stream = add([music, live, playlist])

# Output
output.icecast(
  %mp3,
  stream,
  host="127.0.0.1",
  port=1990,
  password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount="/stream",
)
