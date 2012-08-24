/*
 * Copyright (C) 2012 The Android Open Source Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
package se.uu.it.android.fridaypub;

import java.util.Collection;

import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.text.method.ScrollingMovementMethod;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;

public class SelectedFragment extends Fragment {
	final static String ARG_POSITION = "position";
	int mCurrentPosition = 0;
	protected TextView selection;
	protected TextView editTextStatusUser, editTextStatusPass;
	
	// private final String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
    private final String url = "http://user.it.uu.se/~deklov/fpdb/api.php";
    private final String username = "gurra";
    private final String password = "gurra";

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, 
			Bundle savedInstanceState) {

		// If activity recreated (such as from screen rotate), restore
		// the previous menu selection set by onSaveInstanceState().
		// This is primarily necessary when in the two-pane layout.
		if (savedInstanceState != null) {
			mCurrentPosition = savedInstanceState.getInt(ARG_POSITION);
		}

		// Inflate the layout for this fragment
		return inflater.inflate(R.layout.selected_view, container, false);
	}

	@Override
	public void onStart() {
		super.onStart();

		// During startup, check if there are arguments passed to the fragment.
		// onStart is a good place to do this because the layout has already been
		// applied to the fragment at this point so we can safely call the method
		// below that sets the appropriate text.
		Bundle args = getArguments();
		if (args != null) {
			// Show selected view based on argument passed in
			updateSelectedView(args.getInt(ARG_POSITION));
		} else if (mCurrentPosition != -1) {
			// Show selected view based on saved instance state defined during onCreateView
			updateSelectedView(mCurrentPosition);
		}
	}

	private void useTextView(int position) {
		selection = (TextView) getActivity().findViewById(R.id.selection);
		selection.setText(Ipsum.Views[position]);
		selection.setMovementMethod(new ScrollingMovementMethod());
		mCurrentPosition = position;
	}

	public void drawCredentialView(int position) {
		// XXX Rita upp en vy för att visa/sätta credentials
		selection = (TextView) getActivity().findViewById(R.id.selection);
		selection.setText(Ipsum.Views[position]);
		selection.setMovementMethod(new ScrollingMovementMethod());
		mCurrentPosition = position;
		Button buttonSetPreference = (Button)getActivity().findViewById(R.id.setpreference);
		editTextStatusUser = (TextView)getActivity().findViewById(R.id.edittextstatus_user);
		editTextStatusPass = (TextView)getActivity().findViewById(R.id.edittextstatus_pass);

		buttonSetPreference.setOnClickListener(new Button.OnClickListener(){

			@Override
			public void onClick(View arg0) {
				// TODO Auto-generated method stub
				selection.append("Mer text!");
			}});
	}

	public void updateSelectedView(int position) {

		// XXX Släng in en case switch
		switch (position) {
		case 0:
			useTextView(position);
			new GetIOU().execute("");
			new GetIOUAll().execute("");
			new GetInventory().execute("");
			break;
		case 1:
			useTextView(position);
			new GetInventory().execute("");
			break;
		case 2:
			useTextView(position);
			new GetIOU().execute("");
			new GetIOUAll().execute("");
			break;
		case 4:
			useTextView(position);
			new GetPurchases().execute("");
			break;
		case 7:
			drawCredentialView(position);
		default:
			useTextView(position);
			selection.append("\nNot implemented.\n");
			break;
		}
	}

	@Override
	public void onSaveInstanceState(Bundle outState) {
		super.onSaveInstanceState(outState);

		// Save the current menu selection in case we need to recreate the fragment
		outState.putInt(ARG_POSITION, mCurrentPosition);
	}

	// Throw queries at the FPDB and iterate over results.
	private class GetIOU extends AsyncTask<String, Void, Collection<IOU.Reply>> {
		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		protected Collection<IOU.Reply> doInBackground(String... urls) {
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
			for (IOU.Reply i : reply) {
	        	selection.append("Username: " + i.username + "\n");
	        	selection.append("First name: " + i.first_name + "\n");
	        	selection.append("Last name: " + i.last_name + "\n");
	        	selection.append("Assets: "+ i.assets + "\n");
	        	selection.append("\n");
	        }
			selection.append("\n");
		}
	}
	
	private class GetIOUAll extends AsyncTask<String, Void, Collection<IOUAll.Reply>> {
		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		protected Collection<IOUAll.Reply> doInBackground(String... urls) {
			try {
				return (new IOUAll(url, username, password)).get();
			} catch (FPDBException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			return null;
		}

		/** The system calls this to perform work in the UI thread and delivers
		 * the result from doInBackground() */
		protected void onPostExecute(Collection<IOUAll.Reply> reply) {
			for (IOUAll.Reply i : reply) {
	        	selection.append("Username: " + i.username + "\n");
	        	selection.append("First name: " + i.first_name + "\n");
	        	selection.append("Last name: " + i.last_name + "\n");
	        	selection.append("Assets: "+ i.assets + "\n");
	        	selection.append("\n");
	        }
			selection.append("\n");
		}
	}
	
	private class GetInventory extends AsyncTask<String, Void, Collection<Inventory.Reply>> {
		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		protected Collection<Inventory.Reply> doInBackground(String... urls) {
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
			for (Inventory.Reply i : reply) {
	        	selection.append("Beer ID: " + i.beer_id + "\n");
	        	selection.append("Name: " + i.name + "\n");
	        	selection.append("Price: " + i.price + "\n");
	        	selection.append("Count: "+ i.count + "\n");
	        	selection.append("\n");
	        }
			selection.append("\n");
		}
	}
	
	private class GetPurchases extends AsyncTask<String, Void, Collection<Purchases.Reply>> {
		/** The system calls this to perform work in a worker thread and
		 * delivers it the parameters given to AsyncTask.execute() */
		protected Collection<Purchases.Reply> doInBackground(String... urls) {
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
			for (Purchases.Reply i : reply) {
	        	selection.append("TS: " + i.timestamp + "\n");
	        	selection.append("User ID: " + i.user_id + "\n");
	        	selection.append("Beer ID: " + i.beer_id + "\n");
	        	selection.append("Price: "+ i.price + "\n");
	        	selection.append("\n");
	        }
			selection.append("\n");
		}
	}
}