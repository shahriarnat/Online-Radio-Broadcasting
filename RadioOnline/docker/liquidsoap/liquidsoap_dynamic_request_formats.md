# فرمت‌های پاسخ قابل قبول برای Dynamic Request در Liquidsoap 2.3.3

برای **dynamic request**‌ها (مثل `request.dynamic` یا
`request.dynamic.list`) پاسخ API شما می‌تواند در دو دسته کلی باشد:

## 1) لینک مستقیم به مدیا (یک URI واحد)

-   بدنهٔ پاسخ می‌تواند **فقط یک URI** باشد که Liquidsoap بتواند resolve
    و decode کند.
-   پروتکل‌های پشتیبانی‌شده: **HTTP/HTTPS/FTP** و URIهای داخلی مثل
    `annotate:`, `say:`, `time:`.
-   فرمت‌های صوتی قابل پخش بسته به دیکدرهای نصب‌شده: **MP3/MP2, Ogg
    Vorbis, WAV, AAC** و سایر فرمت‌های پشتیبانی‌شده.

### مثال پاسخ (تک‌خطی):

    http://cdn.example.com/audio/track123.mp3

> اگر دیکدر مربوطه نصب نباشد، درخواست fail می‌شود.

------------------------------------------------------------------------

## 2) یک Playlist استاندارد

سرور می‌تواند به‌جای لینک مستقیم، **یکی از فرمت‌های پلی‌لیستِ پشتیبانی‌شده**
را برگرداند. **Content-Type** باید متناسب با فرمت باشد:

  فرمت          Content-Type                                   حالت
  ------------- ---------------------------------------------- ------------
  PLS           `audio/x-scpls`                                strict
  M3U           `audio/x-mpegurl` یا `audio/mpegurl`           non-strict
  ASX           `video/x-ms-asf` یا `audio/x-ms-asx`           strict
  SMIL          `application/smil` یا `application/smil+xml`   strict
  XSPF          `application/xspf+xml`                         strict
  RSS Podcast   `application/rss+xml`                          strict

### مثال پاسخ PLS

هدر:

    Content-Type: audio/x-scpls

بدنه:

    [playlist]
    NumberOfEntries=1
    File1=http://cdn.example.com/audio/track123.mp3
    Title1=Artist - Title
    Length1=-1

### مثال پاسخ M3U

هدر:

    Content-Type: audio/x-mpegurl

بدنه:

    #EXTM3U
    #EXTINF:-1,Artist - Title
    http://cdn.example.com/audio/track123.mp3

### مثال پاسخ XSPF

هدر:

    Content-Type: application/xspf+xml

بدنه:

``` xml
<?xml version="1.0" encoding="UTF-8"?>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
  <trackList>
    <track>
      <location>http://cdn.example.com/audio/track123.mp3</location>
      <title>Artist - Title</title>
    </track>
  </trackList>
</playlist>
```

------------------------------------------------------------------------

## نکات عملی

-   در callbackِ `request.dynamic` باید یک `request` (مثلاً با
    `request.create(...)`) برگردانی.
-   در `.list` باید آرایه‌ای از `request`‌ها بدهی.
-   تشخیص فرمت پلی‌لیست به **Content-Type** وابسته است.
-   API می‌تواند گاهی لینک مستقیم و گاهی پلی‌لیست برگرداند؛ Liquidsoap هر
    دو را پشتیبانی می‌کند.
