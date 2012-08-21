package se.uu.it.fridaypub;

class FPDBCli
{
    public static void main(String args[])
    {
        String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
        String username = "gurra";
        String password = "gurra";

        FPDBReply<FPDBReplyIOU> reply_iou = null;
        FPDBReply<FPDBReplyInventory> reply_inv = null;
        FPDBReply<FPDBReplyPurchases> reply_pur = null;
        try {
            FPDB db = new FPDB(url, username, password);
            reply_iou = db.iou_get_all();
            reply_inv = db.inventory_get();
            reply_pur = db.purchases_get();
        } catch (Exception e) {
            System.out.println(e.getMessage());
        }

        System.out.println("== IOU ==");
        for (FPDBReplyIOU i : reply_iou) {
        	System.out.println("Username: " + i.username);
        	System.out.println("First name: " + i.first_name);
        	System.out.println("Last name: " + i.last_name);
        	System.out.println("Assets: "+ i.assets);
            System.out.println("");
        }

        System.out.println("== Inventory ==");
        for (FPDBReplyInventory i : reply_inv) {
            System.out.println("name: "  + i.name);
            System.out.println("beer_id: "  + i.beer_id);
            System.out.println("count: "  + i.count);
            System.out.println("price: "  + i.price);
            System.out.println("");
        }

        System.out.println("== Purchases ==");
        for (FPDBReplyPurchases i : reply_pur) {
            System.out.println("user_id: " + i.user_id);
            System.out.println("beer_id: " + i.beer_id);
            System.out.println("price: " + i.price);
            System.out.println("timestamp: " + i.timestamp);
            System.out.println("");
        }
    }
}
