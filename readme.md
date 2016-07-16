[banner]: https://raw.githubusercontent.com/CarlosRios/sermonaudio-php-api/master/banner.jpg "SermonAudio API"

## SermonAudio PHP API
The SermonAudio PHP API allows you to connect to sermon audio easily via your php website or application.

### Uses
If you're already using SermonAudio.com to manage your church's sermons, speakers, sermon series, and events, then using the SermonAudio PHP API will allow you to create web applications or websites with the data you're already providing to SermonAudio.com. This API is great for pulling in  A demo of the SermonAudio PHP API can be found [here](http://crios.me/sermonaudio-api).

#### Connecting to the API
Connecting to the API requires that you first obtain a SermonAudio API key which you can obtain [here](https://www.sermonaudio.com/secure/members_stats.asp).

Once you have an API key, all you need to do is create a new instance of the API and add your key.

```php
$sermon_audio = new SermonAudioAPI;
$sermon_audio->setApiKey( 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX' );
```
