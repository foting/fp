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

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.text.method.ScrollingMovementMethod;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class SelectedFragment extends Fragment {
	final static String ARG_POSITION = "position";
	int mCurrentPosition = 0;
	protected TextView selection;

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

	public void updateSelectedView(int position) {

		// XXX Släng in en case switch
		/*
		 * "Starting Point",	// 0
         * "View Bank",			// 1
         * "View Inventory",	// 2
         * "Buy Beer",			// 3
         * "Purchase History",	// 4
         * "Accept Payment",	// 5
         * "Restock",			// 6
         * "Add User",			// 7
         * "Credentials"		// 8
		 */
		
		switch (position) {
		case 0:
			useTextView(position);
			(new BackendWrapper()).new GetIOUUser().execute(selection);
			(new BackendWrapper()).new GetIOU().execute(selection);
			(new BackendWrapper()).new GetInventory().execute(selection);
			(new BackendWrapper()).new GetPurchases().execute(selection);
			break;
		case 1:
			useTextView(position);
			(new BackendWrapper()).new GetIOUUser().execute(selection);
			(new BackendWrapper()).new GetIOU().execute(selection);
			break;
		case 2:
			useTextView(position);
			(new BackendWrapper()).new GetInventory().execute(selection);
			break;
		case 4:
			useTextView(position);
			(new BackendWrapper()).new GetPurchases().execute(selection);
			break;
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
}