#!/usr/bin/liquidsoap

settings.init.allow_root := true
settings.log.level := 3

settings.server.telnet := true
settings.server.telnet.port := 1234

#settings.crossfade.assume_autocue := true

#settings.autocue.preferred := "internal"

settings.init.force_start := true

# Inputs
#music = mksafe(input.http("http://icecast:1990/music.mp3"))
#playlist = mksafe(playlist("http://backend:8000/track.pls"))
#ch1 = mksafe(input.http("http://backend:8000/track.pls?channel_id=1"))

live  = mksafe(input.http("http://icecast:1990/live"))

# Function to get the next track URL from your API
def fetch_channel_one()
channel_1 = process.read("curl -s -A 'RadioLiquidSoap/1.0' http://backend:8000/track.pls?channel_id=1")
  log("*** channel 1 track URL: #{channel_1}")
  request.create(temporary=true, channel_1)
end

def fetch_channel_two()
channel_2 = process.read("curl -s -A 'RadioLiquidSoap/1.0' http://backend:8000/track.pls?channel_id=2")
  log("*** channel 2 track URL: #{channel_2}")
  request.create(temporary=true, channel_2)
end

# Request source using dynamic API
ch1 = request.dynamic(fetch_channel_one)
stream1 = amplify(1.5, add([live, ch1]))

ch2 = request.dynamic(fetch_channel_two)
stream2 = amplify(1.5, add([blank(), ch2]))

# Output Stream 1
output.icecast(
  %mp3(bitrate=128, samplerate=44100),
  stream1,
  host="icecast",
  port=1990,
  password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount="/stream1",
  buffer=0.1,
  max_buffer=0.5,
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
  max_buffer=0.5,
)
