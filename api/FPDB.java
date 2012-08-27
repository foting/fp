package se.uu.it.fridaypub;

import java.util.Collection;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.NoSuchElementException;

import java.io.IOException;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

interface ReplyFactory<T>
{
    public T create(JSONObject jobj) throws JSONException;
}

class Reply<T> implements Iterable<T>
{
    private LinkedList<T> payload;

    public Reply(String reply, ReplyFactory<T> factory) throws FPDBException
    {
        payload = new LinkedList<T>();
        try {
            JSONObject jobj = new JSONObject(new JSONTokener(reply));

            JSONArray jarr = jobj.getJSONArray("payload");
            for (int i = 0; i < jarr.length(); i++) {
                payload.add(factory.create(jarr.getJSONObject(i)));
            }
        } catch (JSONException e) {
            throw new FPDBException(e);
        }
    }

    public Iterator<T> iterator()
    {
        return payload.iterator();
    }
}

class FPDB
{
    private String url;

    public FPDB(String url, String username, String password)
    {
        this.url = url;
        this.url += "?username=" + username;
        this.url += "&password=" + password;
    }

    private String httpGet(String url) throws FPDBException
    {
        String content;
        try {
            content = HttpGet.get(url);
        } catch (IOException e) {
            throw new FPDBException(e);
        }
        return content;
    }

    public Reply<IOUReply> IOUGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=iou_get");
        return new Reply<IOUReply>(reply, new IOUReplyFactory());
    }

    public Reply<IOUUserReply> IOUUserGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=iou_get_all");
        return new Reply<IOUUserReply>(reply, new IOUUserReplyFactory());
    }

    public Reply<InventoryReply> inventoryGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=inventory_get_all");
        return new Reply<InventoryReply>(reply, new InventoryReplyFactory());
    }

    public Reply<PurchasesReply> purchasesGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=purchases_get_all");
        return new Reply<PurchasesReply>(reply, new PurchasesReplyFactory());
    }
}
