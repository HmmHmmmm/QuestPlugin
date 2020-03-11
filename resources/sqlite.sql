-- #!sqlite
-- #{ questplugin

-- #  { quest
-- #    { init
CREATE TABLE IF NOT EXISTS Quest(
  Name VARCHAR NOT NULL,
  Max INT UNSIGNED NOT NULL,
  Event VARCHAR NOT NULL,
  Info VARCHAR NOT NULL,
  InfoAward VARCHAR NOT NULL,
  CommandAward VARCHAR NOT NULL,
  BoolLimit BOOL NOT NULL,
  ListLimit VARCHAR NOT NULL,
  BoolTrade BOOL NOT NULL,
  Trading VARCHAR NOT NULL,
  PRIMARY KEY(Name)
);
-- #    }

-- #    { load
SELECT * FROM Quest;
-- #    }

-- #    { reset
DELETE FROM Quest;
-- #    }

-- #    { register
-- #      :name string
-- #      :max int
-- #      :event string
-- #      :info string
-- #      :infoAward string
-- #      :commandAward string
-- #      :boolLimit bool
-- #      :listLimit string
-- #      :boolTrade bool
-- #      :trading string
INSERT OR REPLACE INTO Quest(
  Name,
  Max,
  Event,
  Info,
  InfoAward,
  CommandAward,
  BoolLimit,
  ListLimit,
  BoolTrade,
  Trading
) VALUES (
  :name,
  :max,
  :event,
  :info,
  :infoAward,
  :commandAward,
  :boolLimit,
  :listLimit,
  :boolTrade,
  :trading
);
-- #    }

-- #    { unregister
-- #      :name string
DELETE FROM Quest
WHERE Name=:name;
-- #    }

-- #    { save
-- #      :name string
-- #      :max int
-- #      :event string
-- #      :info string
-- #      :infoAward string
-- #      :commandAward string
UPDATE Quest SET
  Max=:max,
  Event=:event,
  Info=:info,
  InfoAward=:infoAward,
  CommandAward=:commandAward
WHERE Name=:name;
-- #    }

-- #    { save2
-- #      :name string
-- #      :boolLimit bool
-- #      :listLimit string
UPDATE Quest SET
  BoolLimit=:boolLimit,
  ListLimit=:listLimit
WHERE Name=:name;
-- #    }

-- #    { save3
-- #      :name string
-- #      :boolTrade bool
-- #      :trading string
UPDATE Quest SET
  BoolTrade=:boolTrade,
  Trading=:trading
WHERE Name=:name;
-- #    }

-- #  }

-- #  { player
-- #    { init
CREATE TABLE IF NOT EXISTS Players(
  Name VARCHAR NOT NULL,
  Quest VARCHAR NOT NULL,
  Start INT UNSIGNED NOT NULL,
  Max INT UNSIGNED NOT NULL,
  Event VARCHAR NOT NULL,
  Info VARCHAR NOT NULL,
  InfoAward VARCHAR NOT NULL,
  CommandAward VARCHAR NOT NULL,
  BoolLimit BOOL NOT NULL,
  ListLimit VARCHAR NOT NULL,
  BoolTrade BOOL NOT NULL,
  Trading VARCHAR NOT NULL,
  PRIMARY KEY(Name, Quest)
);
-- #    }

-- #    { load
SELECT * FROM Players;
-- #    }

-- #    { reset
DELETE FROM Players;
-- #    }

-- #    { register
-- #      :name string
-- #      :quest string
-- #      :start int
-- #      :max int
-- #      :event string
-- #      :info string
-- #      :infoAward string
-- #      :commandAward string
-- #      :boolLimit bool
-- #      :listLimit string
-- #      :boolTrade bool
-- #      :trading string
INSERT OR REPLACE INTO Players(
  Name,
  Quest,
  Start,
  Max,
  Event,
  Info,
  InfoAward,
  CommandAward,
  BoolLimit,
  ListLimit,
  BoolTrade,
  Trading
) VALUES (
  :name,
  :quest,
  :start,
  :max,
  :event,
  :info,
  :infoAward,
  :commandAward,
  :boolLimit,
  :listLimit,
  :boolTrade,
  :trading
);
-- #    }

-- #    { unregister
-- #      :name string
-- #      :quest string
DELETE FROM Players
WHERE Name=:name AND Quest=:quest;
-- #    }

-- #    { save
-- #      :name string
-- #      :quest string
-- #      :start int
-- #      :max int
-- #      :event string
-- #      :info string
-- #      :infoAward string
-- #      :commandAward string
UPDATE Players SET
  Start=:start,
  Max=:max,
  Event=:event,
  Info=:info,
  InfoAward=:infoAward,
  CommandAward=:commandAward
WHERE Name=:name AND Quest=:quest;
-- #    }

-- #    { save2
-- #      :name string
-- #      :quest string
-- #      :boolLimit bool
-- #      :listLimit string
UPDATE Players SET
  BoolLimit=:boolLimit,
  ListLimit=:listLimit
WHERE Name=:name AND Quest=:quest;
-- #    }

-- #    { save3
-- #      :name string
-- #      :quest string
-- #      :boolTrade bool
-- #      :trading string
UPDATE Players SET
  BoolTrade=:boolTrade,
  Trading=:trading
WHERE Name=:name AND Quest=:quest;
-- #    }

-- #  }

-- #}
