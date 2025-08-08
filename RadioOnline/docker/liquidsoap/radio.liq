#!/usr/bin/liquidsoap

################################
# Settings
################################
settings.init.allow_root := true
settings.init.force_start := true
settings.log.level := 3
settings.frame.duration := 0.5
settings.request.prefetch := 1
settings.crossfade.assume_autocue := true
settings.autocue.preferred := "internal"

# Telnet server (بدون احراز هویت: فقط روی شبکه امن/فایروال باز کن)
settings.server.telnet := true
settings.server.telnet.bind_addr := "0.0.0.0"
settings.server.telnet.port := 1234

################################
# Inputs
################################
live_in = input.http("http://icecast:1990/live")
live     = mksafe(id="live_src", live_in)

# --- Dynamic request providers (channel 1 & 2)
def fetch_channel_one() =
  channel_1 = process.read("curl -s -A 'RadioLiquidSoap/1.0' -H 'Accept: audio/x-scpls' http://backend:8000/track?channel_id=1")
  log.critical("*** channel 1 track URL: #{channel_1}")
  request.create(temporary=true, channel_1)
end

def fetch_channel_two() =
  channel_2 = process.read("curl -s -A 'RadioLiquidSoap/1.0' -H 'Accept: audio/x-scpls' http://backend:8000/track?channel_id=2")
  log.critical("*** channel 2 track URL: #{channel_2}")
  request.create(temporary=true, channel_2)
end

# منابع request.dynamic با id برای تلنت
ch1     = request.dynamic(id="ch1_req", fetch_channel_one)
stream1 = amplify(1.5, add([live, ch1]))

ch2     = request.dynamic(id="ch2_req", fetch_channel_two)
stream2 = amplify(1.5, add([blank(), ch2]))

################################
# Outputs (با id برای کنترل تلنت)
################################
output.icecast(
  id="out_stream1",
  %mp3(bitrate=128, samplerate=44100),
  stream1,
  host="icecast",
  port=1990,
  password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount="/stream1",
)

output.icecast(
  id="out_stream2",
  %mp3(bitrate=128, samplerate=44100),
  stream2,
  host="icecast",
  port=1990,
  password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
  mount="/stream2",
)

################################
# Telnet helpers: ساخت/حذف خروجی جدید در زمان اجرا
################################
# انتخاب سورس بر اساس نام
#def pick_source(name) =
#  if name == "stream1" then stream1
#  else if name == "stream2" then stream2
#  else if name == "live" then live
#  else
#    # پیشفرض: خالی
#    blank()
#  end
#end

# دستور: out.add <out_id> <source_name> <mount>
#def out_add(args) =
#  parts = string.split(separator=" ", args)
#  if list.length(parts) != 3 then
#    "Usage: out.add <out_id> <source_name> <mount>"
#  else
#    out_id      = list.nth(parts,0)
#    source_name = list.nth(parts,1)
#    mount_pt    = list.nth(parts,2)
#    src         = pick_source(source_name)
#    if src == blank() and source_name != "blank" then
#      "Error: unknown source_name. Use one of: stream1, stream2, live, blank"
#    else
#      # خروجی جدید ایجاد می‌کنیم؛ با id سفارشی تا تلنت کنترل‌پذیر باشد
#      output.icecast(
#        id=out_id,
#        %mp3(bitrate=128, samplerate=44100),
#        src,
#        host="icecast",
#        port=1990,
#        password="source.r5UeMAsbJFfNwuW9ycCgqd2Gt",
#        mount=mount_pt,
#      )
#      "OK: created output '"^out_id^"' on mount '"^mount_pt^"' from source '"^source_name^"'."
#    end
#  end
#end

# دستور: out.del <out_id>  (توقف کامل و حذف)
# در 2.3.x بهترین معادل «ریست/حذف» استفاده از shutdown است.
#def out_del(args) =
#  out_id = string.trim(args)
#  if out_id == "" then
#    "Usage: out.del <out_id>"
#  else
#    # معادل reset: stop -> shutdown (می‌تونی بعداً دوباره add کنی)
#    server.execute(out_id^".stop")
#    server.execute(out_id^".shutdown")
#    "OK: '"^out_id^"' stopped and shutdown."
#  end
#end

# ثبت دستورات در تلنت
#server.register(namespace="out",
#  description="Create a new icecast output from an existing source.",
#  usage="out.add <out_id> <source_name> <mount>",
#  "add", out_add)
#
#server.register(namespace="out",
#  description="Delete (stop+shutdown) an existing output by id.",
#  usage="out.del <out_id>",
#  "del", out_del)
#
################################
# (اختیاری) Web/Telnet ux بهتر:
# می‌توانی interactive.harbor هم راه بیندازی، اگر خواستی UI وب ساده داشته باشی.
################################
