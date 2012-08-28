package se.uu.it.android.fridaypub;

import se.uu.it.fridaypub.FPDB;
import se.uu.it.fridaypub.FPDBException;
import se.uu.it.fridaypub.IOUReply;
import se.uu.it.fridaypub.IOUUserReply;
import se.uu.it.fridaypub.InventoryReply;
import se.uu.it.fridaypub.PurchasesReply;
import se.uu.it.fridaypub.Reply;
import android.os.AsyncTask;
import android.widget.TextView;

public class BackendWrapper {
	private final String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
    //private final String url = "http://user.it.uu.se/~deklov/fpdb/api.php";
    private final String username = "gurra";
    private final String password = "gurra";
    
	// Throw queries at the FPDB and iterate over results.
	protected class GetIOU extends AsyncTask<TextView, Void, Reply<IOUReply>> {
		private TextView selection;

		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		protected Reply<IOUReply> doInBackground(TextView... v) {
			selection = v[0];

            Reply<IOUReply> reply = null;
			try {
				reply = (new FPDB(url, username, password)).IOUGet();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return reply;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Reply<IOUReply> reply) {
			try {
				for (IOUReply i : reply) {
					selection.append("Username: " + i.username + "\n");
					selection.append("First name: " + i.first_name + "\n");
					selection.append("Last name: " + i.last_name + "\n");
					selection.append("Assets: "+ i.assets + "\n");
					selection.append("\n");
				}
				selection.append("\n");
			} catch (Exception e) {
				selection.append("\n" + e + "\n");
			}
		}
	}
	
	protected class GetIOUUser extends AsyncTask<TextView, Void, Reply<IOUUserReply>> {
		private TextView selection;

		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		protected Reply<IOUUserReply> doInBackground(TextView... v) {
			selection = v[0];

            Reply<IOUUserReply> reply = null;
			try {
				reply = (new FPDB(url, username, password)).IOUUserGet();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return reply;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Reply<IOUUserReply> reply) {
			try {
				for (IOUUserReply i : reply) {
					selection.append("User ID: " + i.user_id + "\n");
					selection.append("First name: " + i.first_name + "\n");
					selection.append("Last name: " + i.last_name + "\n");
					selection.append("Assets: "+ i.assets + "\n");
					selection.append("\n");
				}
				selection.append("\n");
			} catch (Exception e) {
				selection.append("\n" + e + "\n");
			}
		}
	}
	
	protected class GetInventory extends AsyncTask<TextView, Void, Reply<InventoryReply>> {
		private TextView selection;

		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		protected Reply<InventoryReply> doInBackground(TextView... v) {
			selection = v[0];

            Reply<InventoryReply> reply = null;
			try {
				reply = (new FPDB(url, username, password)).inventoryGet();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return reply;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Reply<InventoryReply> reply) {
			try {
				for (InventoryReply i : reply) {
					selection.append("Beer ID: " + i.beer_id + "\n");
					selection.append("Name: " + i.name + "\n");
					selection.append("Price: " + i.price + "\n");
					selection.append("Count: "+ i.count + "\n");
					selection.append("\n");
				}
				selection.append("\n");
			} catch (Exception e) {
				selection.append("\n" + e + "\n");
			}
		}
	}
	
	protected class GetPurchases extends AsyncTask<TextView, Void, Reply<PurchasesReply>> {
		private TextView selection;

		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		protected Reply<PurchasesReply> doInBackground(TextView... v) {
			selection = v[0];

            Reply<PurchasesReply> reply = null;
			try {
				reply = (new FPDB(url, username, password)).purchasesGet();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return reply;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Reply<PurchasesReply> reply) {
			try {
				for (PurchasesReply i : reply) {
					selection.append("TS: " + i.timestamp + "\n");
					selection.append("User ID: " + i.user_id + "\n");
					selection.append("Beer ID: " + i.beer_id + "\n");
					selection.append("Price: "+ i.price + "\n");
					selection.append("\n");
				}
				selection.append("\n");
			} catch (Exception e) {
				selection.append("\n" + e + "\n");
			}
		}
	}
}
