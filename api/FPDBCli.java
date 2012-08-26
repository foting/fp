package se.uu.it.fridaypub;

import java.util.*;

class FPDBCli
{
    public static void main(String args[])
    {
        //String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
        String url = "http://user.it.uu.se/~deklov/fpdb/api.php";
        String username = "gurra";
        String password = "gurra";

        Collection<IOU.Reply> iou_r = null;
        Collection<Purchases.Reply> pur_r = null;
        Collection<Inventory.Reply> inv_r = null;
        try {
            iou_r = (new IOU(url, username, password)).get();
            pur_r = (new Purchases(url, username, password)).get();
            inv_r = (new Inventory(url, username, password)).get();
        } catch (Exception e) {
            System.out.println(e.getMessage());
            System.exit(-1);
        }

        System.out.println("== IOU ==");
        for (IOU.Reply i : iou_r) {
        	System.out.println("Username: " + i.username);
        	System.out.println("First name: " + i.first_name);
        	System.out.println("Last name: " + i.last_name);
        	System.out.println("Assets: "+ i.assets);
            System.out.println("");
        }

        System.out.println("== Purchases ==");
        for (Purchases.Reply i : pur_r) {
            System.out.println("user_id: " + i.user_id);
            System.out.println("beer_id: " + i.beer_id);
            System.out.println("price: " + i.price);
            System.out.println("timestamp: " + i.timestamp);
            System.out.println("");
        }

        System.out.println("== Inventory ==");
        for (Inventory.Reply i : inv_r) {
            System.out.println("name: " + i.name);
            System.out.println("beer_id: " + i.beer_id);
            System.out.println("count: " + i.count);
            System.out.println("price: " + i.price);
            System.out.println("");
        }

    }
}
