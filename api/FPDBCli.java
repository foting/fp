package se.uu.it.fridaypub;

import java.util.*;

class FPDBCli
{
    static String url = "http://user.it.uu.se/~deklov/fpdb/api.php";
    static String username;
    static String password;
    static String action;

    private static void exit(String msg)
    {
        System.out.println(msg);
        System.exit(-1);
    }

    private static void print_iou(Reply<IOUReply> reply)
    {
        for (IOUReply i : reply) {
        	System.out.println("Username: " + i.username);
        	System.out.println("First name: " + i.first_name);
        	System.out.println("Last name: " + i.last_name);
        	System.out.println("Assets: "+ i.assets);
            System.out.println("");
        }
    }

    private static void print_inventory(Reply<InventoryReply> reply)
    {
    }

    private static void print_purchases(Reply<PurchasesReply> reply)
    {
    }

    
    private static void print_payments(Reply<PaymentsReply> reply)
    {
        for (PaymentsReply i : reply) {
        	System.out.println("User ID: " + i.user_id);
        	System.out.println("Admin ID: " + i.admin_id);
        	System.out.println("amount: " + i.amount);
            System.out.println("");
        }
 
    }

    private static void do_action() throws FPDBException
    {
        FPDB fpdb = new FPDB(url, username, password);

        if (action.equals("iou_get")) {
            print_iou(fpdb.IOUGet());
        } else if (action.equals("inventory")) {
            print_inventory(fpdb.inventoryGet());
        } else if (action.equals("purchases")) {
            print_purchases(fpdb.purchasesGet());
        } else if (action.equals("payments")) {
            print_payments(fpdb.paymentsGet());
        } else {
            exit("Error: Unknown action");
        }
    }

    public static void main(String argv[])
    {
        /*
        if (argv.length != 3) {
            exit("Usage: FPDBCli <username> <password> <action>");
        }
        */
        username = "gurra"; //argv[0];
        password = "gurra"; //argv[1];
        action = "payments"; //argv[2];

        try {
            do_action();
        } catch (FPDBException e) {
            exit(e.getMessage());
        }
    }
}
