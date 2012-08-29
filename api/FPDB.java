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
        String reply = httpGet(url + "&action=iou_get_all");
        return new Reply<IOUReply>(reply, new IOUReplyFactory(), "iou_get");
    }

    public Reply<IOUUserReply> IOUUserGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=iou_get");
        return new Reply<IOUUserReply>(reply, new IOUUserReplyFactory(), "iou_get_all");
    }


    public Reply<InventoryReply> inventoryGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=inventory_get_all");
        return new Reply<InventoryReply>(reply, new InventoryReplyFactory(), "inventory_get_all");
    }


    public Reply<PurchasesReply> purchasesGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=purchases_get_all");
        return new Reply<PurchasesReply>(reply, new PurchasesReplyFactory(), "purchases_get_all");
    }

    public Reply<EmptyReply> purchasesAppend(int beer_id) throws FPDBException
    {
        String reply = httpGet(url + "&action=purchases_append&beer_id=" + beer_id);
        return new Reply<EmptyReply>(reply, new EmptyReplyFactory(), "empty");
    }


    public Reply<PaymentsReply> paymentsGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=payments_get_all");
        return new Reply<PaymentsReply>(reply, new PaymentsReplyFactory(), "payments_get_all");
    }

    public Reply<PaymentsReply> paymentsUserGet() throws FPDBException
    {
        String reply = httpGet(url + "&action=purchases_get");
        return new Reply<PaymentsReply>(reply, new PaymentsReplyFactory(), "payments_get");
    }

    public Reply<EmptyReply> purchasesAppend(int user_id, int amount) throws FPDBException
    {
        String reply = httpGet(url + "&action=payments_append&user_id=" + user_id + "&amount=" + amount);
        return new Reply<EmptyReply>(reply, new EmptyReplyFactory(), "empty");
    }
}
