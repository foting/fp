package se.uu.it.fridaypub;

import java.io.IOException;

public class FPDB
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
