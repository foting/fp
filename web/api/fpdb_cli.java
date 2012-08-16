import java.util.*;
import java.io.*; 
import org.json.simple.*;


class FPDB_Error extends Exception
{
    public FPDB_Error(Exception e)
    {
        super(e);
    }

    public FPDB_Error(String m)
    {
        super(m);
    }
}

class FPDB_Reply implements Iterable<Map<String, String>>
{
    public String type;
    Iterator<Map<String, String>> iter;

    public FPDB_Reply(JSONObject jobj) throws FPDB_Error
    {
        type = (String)jobj.get("type");
        if (type == null) {
            throw new FPDB_Error("Reply has no type attribute");
        }

        JSONArray jarray = (JSONArray)jobj.get("payload");
        if (type == null) {
            throw new FPDB_Error("Reply has no payload attribute");
        }

        iter = ((List<Map<String, String>>)jarray).iterator();
    }

    public Iterator<Map<String, String>> iterator()
    {
        return iter;
    }
}

class FPDB_Api
{
    public static JSONObject http_get(String url) throws FPDB_Error
    {
        String jstr = "";
        JSONObject jobj;

        /* Replace this with an actual http get or post */
        try {
            BufferedReader br = new BufferedReader(new FileReader(url));
            
            for (String s; (s = br.readLine()) != null; ) {
                jstr += s;
            }
        } catch (IOException e) {
            throw new FPDB_Error(e);
        }
            
        jobj = (JSONObject)JSONValue.parse(jstr);
        if (jobj == null) {
            throw new FPDB_Error("Parsing failed");
        }

        return jobj;
    }


    public static FPDB_Reply request(String url) throws FPDB_Error
    {
        return new FPDB_Reply(http_get(url));
    }
}



class FPDB_Cli
{
    public static void main(String[] args)
    {
        FPDB_Reply reply = null;

        if (args.length != 1) {
            System.out.println("Usage: FPDB_Cli url");
            System.exit(-1);
        }

        try {
            reply = FPDB_Api.request(args[0]);
        } catch (FPDB_Error e) {
            System.out.println(e);
            System.exit(-1);
        }

        System.out.println(reply.type);
        for (Map<String, String> i : reply) {
            for (Map.Entry<String, String> j : i.entrySet()) {
                System.out.println(j.getKey() + " = " + j.getValue());
            }
        }
    }
}
