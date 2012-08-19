import java.util.*;
import java.io.*; 
import java.net.*;
import org.json.simple.*;


class FPDBException extends Exception
{
    public FPDBException(String msg)
    {
        super(msg);
    }
}


interface FPDBReplyFactory<T>
{  
    T create(Map<String, String> map) throws FPDBException;
} 


class FPDBReply<T>
{
    LinkedList<T> payload;

    public FPDBReply(String jstr, FPDBReplyFactory<T> factory) throws FPDBException
    {
        JSONObject jobj = (JSONObject)JSONValue.parse(jstr);
        if (jobj == null) {
            throw new FPDBException("JSON Parser error");
        }

        String type = (String)jobj.get("type");
        if (type == null) {
            throw new FPDBException("JSON no type attribute");
        }

        if (type.equals("error")) {
            throw new FPDBException("TODO: Get error message");
        }

        List<Map<String, String>> payload_map = (List<Map<String, String>>)jobj.get("payload");
        if (payload_map == null) {
            throw new FPDBException("JSON no payload attribute");
        }

        payload = new LinkedList<T>();
        for (Map<String, String> i : payload_map) {
            payload.add(factory.create(i));
        }
    }
}


class _FPDBReplyInventory
{
    public String name;
    public int    beer_id;
    public int    count;
    public float  price;

    public _FPDBReplyInventory(Map<String, String> map) throws FPDBException
    {
        name = map.get("name");
        beer_id = Integer.parseInt(map.get("beer_id"));
        count = Integer.parseInt(map.get("count"));
        price = Float.parseFloat(map.get("price"));
    }

}

class _FPDBReplyIOU
{
    public String username;
    public String first_name;
    public String last_name;
    public float  assets;

    public _FPDBReplyIOU(Map<String, String> map) throws FPDBException
    {
        username = map.get("username");
        first_name = map.get("first_name");
        last_name = map.get("last_name");
        assets = Float.parseFloat(map.get("assets"));
    }
}

/*
 * Factory classes
 */
class FPDBReplyInventory_Factory implements FPDBReplyFactory<_FPDBReplyInventory>
{
    public _FPDBReplyInventory create(Map<String, String> map) throws FPDBException
    {
        return new _FPDBReplyInventory(map);
    }
}

class FPDBReplyIOU_Factory implements FPDBReplyFactory<_FPDBReplyIOU>
{
    public _FPDBReplyIOU create(Map<String, String> map) throws FPDBException
    {
        return new _FPDBReplyIOU(map);
    }
}

/*
 * Essentially typedefs
 */
class FPDBReplyInventory extends FPDBReply<_FPDBReplyInventory>
{
    public FPDBReplyInventory(String jstr) throws FPDBException
    {
        super(jstr, new FPDBReplyInventory_Factory());
    }
}

class FPDBReplyIOU extends FPDBReply<_FPDBReplyIOU>
{
    public FPDBReplyIOU(String jstr) throws FPDBException
    {
        super(jstr, new FPDBReplyIOU_Factory());
    }
}



class FPDB
{
    private String url;
    private String username;
    private String password;

    public FPDB(String url, String username, String password)
    {
        this.username = username;
        this.password = password;
        this.url = String.format("%s?username=%s&password=%s", url, username, password);
    }

    private String http_get_json(String url) throws IOException
    {
        URLConnection con = (new URL(url)).openConnection();
        InputStreamReader sr = new InputStreamReader(con.getInputStream());
        BufferedReader br = new BufferedReader(sr);

        String line, jstr = "";
        while ((line = br.readLine()) != null) {
            jstr += line;
        }

        return jstr;
    }

    public FPDBReplyInventory inventory_get() throws FPDBException, IOException
    {
        String jstr = http_get_json(url + "&action=inventory_get");
        return new FPDBReplyInventory(jstr);
    }

    public FPDBReplyIOU iou_get() throws FPDBException, IOException
    {
        String jstr = http_get_json(url + "&action=iou_get");
        return new FPDBReplyIOU(jstr);
    }

    public FPDBReplyIOU iou_get_all() throws FPDBException, IOException
    {
        String jstr = http_get_json(url + "&action=iou_get_all");
        return new FPDBReplyIOU(jstr);
    }
}


