## QuestPlugin


[Language English](#english)


Ultimate quest plugin, create quest has unlimited And can create many forms


download QuestPlugin.phar dev https://poggit.pmmp.io/ci/HmmHmmmm/QuestPlugin/QuestPlugin

![1](https://github.com/HmmHmmmm/QuestPlugin/blob/master/images/3.1/1.jpg)

# English

```diff
You must install the plugin
- Slapper
this plugin will work
```

Download the plugin Slapper [Click here](https://poggit.pmmp.io/p/slapper)

**Features of plugin**<br>
- Is a plugin to create quests using [Event](#event)
- support database Yaml and SQLite
- Have language thai and english (You can edit the language you don't like at, `plugin_data/QuestPlugin/language`)


**How to use**<br>
- https://youtu.be/jw8HKls-H4w


**Command**<br>
- `/quest` : open gui form

# Event
- breakblock (Break Block)
- placeblock (Place Block)
- kill_entity (Kill living things)
- kill_player (Kill players)
- trading (Trading#Please hold item.)
- online (Online)


# Config
```
#thai=ภาษาไทยนะจ้ะ
#english=English language
#You can edit in plugin_data/QuestPlugin/language
language: english

#yml=Yaml, Information will be in plugin_data/QuestPlugin/quest.yml
#sqlite=SQLite3, Information will be in plugin_data/QuestPlugin/quest.sqlite3
questdata-database: sqlite

#yml=Yaml, Information will be in plugin_data/QuestPlugin/account
#sqlite=SQLite3, Information will be in plugin_data/QuestPlugin/players.sqlite3
playerdata-database: yml

slapper-update: 10

slapper-type: Human

#It is now not unusable.
#MySQL-Info:
  #Host: 127.0.0.1
  #User: Admin
  #Password: Admin
  #Database: QuestPlugin
  #Port: 3306
```
  

# Permissions
```
/*
*
* Command /quest it can be typed by everyone.
*
*/
quest.command.info:
  default: op
quest.command.list:
  default: true
quest.command.remove:
  default: true
quest.command.add:
  default: op
quest.command.data:
  default: false
quest.command.data.gui:
  default: op
quest.command.data.event:
  default: opquest.command.data.create:
  default: op
quest.command.data.remove:
  default: op
quest.command.data.list:
  default: op
quest.command.data.setlimit:
  default: op
quest.command.data.addlimit:
  default: op
quest.command.data.slapperget:
  default: op
```


