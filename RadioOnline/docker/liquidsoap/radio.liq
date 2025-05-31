#!/usr/bin/liquidsoap

settings.init.allow_root := true
settings.log.level := 2

settings.server.telnet := true
settings.server.telnet.port := 1234

settings.crossfade.assume_autocue := true

settings.autocue.preferred := "internal"

settings.init.force_start := true

# Inputs
#music = mksafe(input.http("http://icecast:1990/music.mp3"))
#playlist = mksafe(playlist("http://backend:8000/track.pls"))

music = mksafe(input.http("https://n05.radiojar.com/cp13r2cpn3quv?rj-ttl=5&rj-tok=AAABlyN2P8UAZhFQuQmWGHCCvQ"))
live  = mksafe(input.http("http://icecast:1990/live"))
english  = mksafe(input.http("https://dl7.irlanguage.com/ESLPodcast-0001-0050/Daily%20English%200001-Introducing%20Yourself.mp3"))

# Function to get the next track URL from your API
def fetch_next_track()
  url = process.read("curl -s http://backend:8000/track.pls")
  log("Next track URL: #{url}")
  request.create(url)
end

# Request source using dynamic API
playlist = request.dynamic(fetch_next_track)

stream1 = amplify(2.5, add([music, live, playlist]))
stream2 = amplify(2.5, english)

# Output Stream 1
output.icecast(
  %mp3,
  stream1,
  host="icecast",
  port=1990,
  password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount="/stream1",
)

# Output Stream 2
output.icecast(
  %mp3,
  stream2,
  host="icecast",
  port=1990,
  password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount="/stream2",
)
