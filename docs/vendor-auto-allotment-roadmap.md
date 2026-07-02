# Vendor Auto-Allotment Roadmap

## Goal

Build a city/state vendor network where Kids Party Planner stays the customer-facing brand, while approved vendors receive assigned local work and settle commission through their dashboard.

## Suggested Phases

1. Vendor onboarding
   - Vendor registration with name, phone, email, city, state, service categories, documents and approval status.
   - Admin approval, service mapping and city coverage settings.

2. Assignment workflow
   - Booking enters `Confirmed` after payment.
   - Admin can assign manually to a vendor based on city, area, date and service category.
   - Future auto-allotment can score vendors by city match, availability, rating, workload and commission terms.

3. Vendor dashboard
   - Assigned jobs, customer/event details, status updates, upload proof, earnings and payout history.
   - Vendor cannot see platform-wide customers or other vendor data.

4. Commission and settlement
   - Store platform commission percent/fixed amount per booking.
   - Split booking earning into vendor earning and platform commission.
   - Vendor withdrawal requests with admin approval and payout reference.

5. Tables to add later
   - `vendors`, `vendor_service`, `vendor_availabilities`, `booking_assignments`, `vendor_earnings`, `vendor_withdrawals`, `vendor_documents`.

## First Safe Implementation

Start with manual assignment and vendor dashboard before automatic matching. Once real booking volume exists, add auto-allotment rules using the same assignment tables.
