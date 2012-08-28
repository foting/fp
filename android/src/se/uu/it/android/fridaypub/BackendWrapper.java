package se.uu.it.android.fridaypub;

import java.util.Collection;

import se.uu.it.fridaypub.FPDBException;
import se.uu.it.fridaypub.IOU;
import se.uu.it.fridaypub.IOUUser;
import se.uu.it.fridaypub.Inventory;
import se.uu.it.fridaypub.Purchases;
import android.os.AsyncTask;
import android.widget.TextView;

public class BackendWrapper {
	private final String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
    //private final String url = "http://user.it.uu.se/~deklov/fpdb/api.php";
    private final String username = "gurra";
    private final String password = "gurra";
    
	// Throw queries at the FPDB and iterate over results.
	protected class GetIOU extends AsyncTask<TextView, Void, Collection<IOU.Reply>> {
		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		private TextView selection;
		protected Collection<IOU.Reply> doInBackground(TextView... v) {
			selection = v[0];
			try {
				return (new IOU(url, username, password)).get();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return null;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Collection<IOU.Reply> reply) {
			try {
				for (IOU.Reply i : reply) {
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
	
	protected class GetIOUUser extends AsyncTask<TextView, Void, Collection<IOUUser.Reply>> {
		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		private TextView selection;
		protected Collection<IOUUser.Reply> doInBackground(TextView... v) {
			selection = v[0];
			try {
				return (new IOUUser(url, username, password)).get();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return null;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Collection<IOUUser.Reply> reply) {
			try {
				for (IOUUser.Reply i : reply) {
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
	
	protected class GetInventory extends AsyncTask<TextView, Void, Collection<Inventory.Reply>> {
		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		private TextView selection;
		protected Collection<Inventory.Reply> doInBackground(TextView... v) {
			selection = v[0];
			try {
				return (new Inventory(url, username, password)).get();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return null;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Collection<Inventory.Reply> reply) {
			try {
				for (Inventory.Reply i : reply) {
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
	
	protected class GetPurchases extends AsyncTask<TextView, Void, Collection<Purchases.Reply>> {
		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		private TextView selection;
		protected Collection<Purchases.Reply> doInBackground(TextView... v) {
			selection = v[0];
			try {
				return (new Purchases(url, username, password)).get();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return null;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Collection<Purchases.Reply> reply) {
			try {
				for (Purchases.Reply i : reply) {
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
