=== WP Time Slots Booking Form ===
Contributors: codepeople
Donate link: https://wptimeslot.dwbooster.com/download
Tags: booking,booking calendar,time,slot,reservation
Requires at least: 3.0.5
Tested up to: 6.8
Stable tag: 1.2.32
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Time Slots Booking Form is a booking calendar that allows users to reserve time slots on specific dates.

== Description ==

**WP Time Slots Booking Form** is a powerful **booking calendar** solution that enables users to create customized **booking forms** for **appointment booking** on specific calendar dates. With this intuitive tool, you can design a user-friendly form featuring a calendar where end-users can easily select available time slots and complete their bookings.

Upon booking completion, notification emails are automatically sent to the admin, ensuring they are informed of every **calendar booking**. Additionally, a confirmation email can be dispatched to the user who made the booking, enhancing the overall user experience.

This versatile **booking calendar** system is ideal for a variety of applications, including reserving classrooms, purchasing event tickets, scheduling medical appointments, booking times in escape rooms, arranging personal coaching or professional assistance, and securing cleaning services. It is perfect for any service that requires customers to select specific date-times or groups of date-times (time slots) from a set of available options, making it an essential tool for effective **appointment booking**.

In the **booking calendar** you can setup:

* **Available time slots** for each weekday
* **Available time slots** on specific dates
* Capacity (number of persons that can book) for each time slot
* The number of different time slots that can be selected in a single booking
* Minimum and maximum available dates
* Holiday or closed dates
* Price for each time slot
* Pricing for different numbers of selected slots
* Pricing options based on the number of adults and children (optional)
* Number of months to display
* Additional calendar features

In addition to these **calendar booking** functionalities, other important features of the plugin include:
 
* A modern, mobile-friendly design
* A responsive calendar and booking form
* A visual form builder for easy customization
* Multi-language support for global accessibility
* Notification emails to keep users informed
* Anti-spam features to protect against unwanted submissions
* Email reports for tracking bookings
* CSV reports for data analysis
* A usage and stats area for insights
* A bookings list for easy management
* A printable schedule list for offline reference
* A multi-view calendar for displaying schedules
* Integration with Elementor, Gutenberg, and other page builders
* Support for add-ons, including the iCal add-on
 
The plugin effectively manages the availability of each time slot, allowing you to define a maximum capacity for each slot and set the maximum number of time slots that a customer can select for their booking.

You can optionally enable multiple individuals to book the same time slots until the capacity is fully filled. The plugin also allows you to set different prices for two groups (for example, adults and children) and includes additional options that make it suitable for purchasing event tickets or other activities/items with varying capacities.

= Features in commercial versions =

While the free version of the plugin is fully functional, the commercial versions offer premium features that enhance its capabilities, including:

* **Payment Integration**: Support for various payment gateways such as PayPal, Stripe, Skrill, Authorize.net, iDEAL, SagePay, and Redsys.
* **SCA Ready Payments**: Payments are compliant with Strong Customer Authentication (SCA) and compatible with the new Payment Services Directive (PSD2) in the EU.
* **iCal Synchronization**: Easily import and export calendar events with [iCal import](https://wptimeslot.dwbooster.com/blog/2018/12/20/ical-import/) and [iCal export](https://wptimeslot.dwbooster.com/blog/2018/12/19/adding-google-iphone-outlook/).
* **Integration with External Services**: Seamless integration with services like reCaptcha, MailChimp, Salesforce, WooCommerce, and more.
* **SMS Notifications**: Integration with SMS services via Twilio or Clickatell for booking confirmations and reminders.
* **Booking Reminders**: Automated reminders to keep users informed about their bookings.
* **Rich Form Builder**: Create advanced forms with conditional fields, multi-page forms, file uploads, and more.
* [**Additional Items Fields**](https://wptimeslot.dwbooster.com/blog/2018/12/15/additional-services/): Customize your forms with additional fields for specific requirements.
* [**Email Notifications**](https://wptimeslot.dwbooster.com/blog/2018/11/28/status-update-emails/): Stay updated with email notifications on booking status changes.

For a complete list of commercial features, please visit the [plugin download page](https://wptimeslot.dwbooster.com/download).



== Installation ==

To install **WP Time Slots Booking Form**, follow these steps:

1.	Download and unzip the WP Time Slots Booking Form calendar plugin
2.	Upload the entire appointment-hour-booking/ directory to the /wp-content/plugins/ directory
3.	Activate the WP Time Slots Booking Form plugin through the Plugins menu in WordPress
4.	Configure the settings at the administration menu >> Settings >> WP Time Slots Booking Form. 
5.	To insert the WP Time Slots Booking Form calendar form into some content or post use the icon that will appear when editing contents


== Frequently Asked Questions ==

= Q: Where can I find the complete WP Time Slots Booking Form plugin documentation? =

A: The product's page contains detailed documentation and support: [https://wptimeslot.dwbooster.com/support](https://wptimeslot.dwbooster.com/support)

= Q: How can I customize the styles? =

A: Please check the complete instructions on this page: [Customizing Styles](https://wptimeslot.dwbooster.com/blog/2018/11/02/customizing-styles/)

= Q: Can I display a list of appointments? =

A: You can display a list of appointments set on the calendar by using the following shortcode on the page where you want to show the list:

[CP_TIME_SLOTS_BOOKING_LIST]

For additional details, refer to this FAQ entry: [FAQ Entry](https://wptimeslot.dwbooster.com/faq#q511) and more information at this page: [Grouped Frontend Lists](https://wptimeslot.dwbooster.com/blog/2018/11/21/grouped-frontend-lists/)

= Q: I'm not receiving the emails with the appointment data. =

A: First, try using a "from" email address that belongs to your website domain, as this is the most common restriction applied by hosting services. 

If that doesn't work, please check if your hosting service requires specific configurations to send emails from PHP/WordPress websites. The plugin uses the settings specified in your WordPress site to deliver emails. If your hosting has specific requirements, such as a fixed "from" address or a custom "SMTP" server, those settings must be configured in your WordPress site.

= Q: How can I change the styles of the dates based on the number of booked/available slots? =

A: You can set different colors/styles for the dates depending on the number of booked or available slots, allowing users to see the availability at a glance. This feature is useful for indicating dates with limited slots, prompting customers to act quickly.

Detailed instructions are available on this page: [Booked Date Colors](https://wptimeslot.dwbooster.com/blog/2019/05/10/booked-date-colors/)

= Q: Can I export the bookings to external calendars? =

A: Yes, you can export bookings using the iCal export add-on included in all versions of the plugin. The process is described in detail on this page: [Adding Google, iPhone, Outlook](https://wptimeslot.dwbooster.com/blog/2018/12/19/adding-google-iphone-outlook/)

= Q: I'm getting API errors while using the Zoom integration. =

A: If you encounter API errors while creating a Zoom meeting via API, please refer to the Zoom developer documentation regarding changes during COVID-19: [Developer Impacting Changes](https://devforum.zoom.us/t/developer-impacting-changes-during-covid-19/8930)

= Q: How can I align the form using various columns? =

A: The solution is described in the following FAQ entry: [FAQ Entry](https://wptimeslot.dwbooster.com/faq#q66)

= Q: How can I add specific fields to the email message? =

A: Please refer to this FAQ entry for available tags to customize the emails: [FAQ Entry](https://wptimeslot.dwbooster.com/faq#q81)

= Q: How do I make the calendar 100% width / responsive? =

A: Use the following CSS style to make the WP Time Slots Booking Form 100% width and responsive on the page:

    #fbuilder .ui-datepicker-inline { max-width: none !important; }

Add the styles in the "WP Time Slots Booking Form >> General Settings >> Edit Styles" area.

= Q: What additional add-ons are available for this booking calendar plugin? =

The following is a sample of add-ons available for the WP Time Slots Booking Form plugin. Some of these add-ons are included in the free version, while others are part of the commercial versions:

* **reCAPTCHA**: Protect your **booking forms** with Google's reCAPTCHA service to prevent spam submissions.
* **Set Order of Payment Options**: Control the order in which payment options appear in the **booking calendar** when multiple payment methods are enabled.
* **Email Blacklist**: Prevent bookings from blacklisted email addresses to enhance security.
* **Dashboard Widget - Upcoming Appointments**: Add a widget to the dashboard welcome page that displays a list of upcoming appointments for easy access.
* **Follow-Up Notifications for Bookings**: Enable support for follow-up notifications to keep users informed about their **appointment bookings**.
* **Clickatell**: Send SMS notification messages via Clickatell after form submission to ensure timely communication.
* **Additional Booking Statuses**: Add new statuses to your bookings for better management and tracking.
* **Import Raw Bookings (CSV Format)**: Import bookings in raw CSV format for seamless data integration.
* **Signature Fields**: Replace standard form fields with "Signature" fields for enhanced user verification.
* **Uploads**: Allow users to upload files, which will be added to the Media Library, with support for new MIME types.
* **WebHook**: Send submitted information to a webhook URL, integrating your **booking calendar** with services like Zapier.
* **Authorize.net Server Integration Method**: Support for Authorize.net Server Integration Method payments for secure transactions.
* **Automatically Cancel Pending Bookings**: Automatically cancel pending bookings after a specified expiration time.
* **Cancellation Link for Bookings**: Provide cancellation links for bookings to enhance user flexibility.
* **Coupons**: Add support for coupon and discount codes to incentivize bookings.
* **Deposit Payments**: Accept payment deposits as a fixed amount or a percentage of the total booking cost.
* **Double Opt-In Email Verification**: Implement double opt-in email verification to mark bookings as approved.
* **Frontend List - Grouped by Date Add-on**: Display a list of bookings grouped by date on the frontend for better organization.
* **Google Calendar API**: Integrate with Google Calendar API for seamless calendar synchronization.
* **iCal Export Addon**: Export bookings as iCal files for easy integration with other calendar applications.
* **iCal Automatic Import**: Automatically import iCal files from external websites or services to keep your **calendar booking** up to date.
* **MailChimp**: Create MailChimp list members with the submitted information for effective email marketing.
* **iDeal Mollie**: Support for iDeal payments via Mollie for convenient transactions.
* **PayPal Standard Payments Integration**: Integrate PayPal Standard payments for secure online transactions.
* **PayPal Pro**: Accept credit card payments directly on your website with PayPal Payment Pro.
* **QRCode Image - Barcode**: Generate a QRCode image for each booking for easy access and verification.
* **RedSys TPV**: Support for RedSys TPV payments, popular in Spain, for local transactions.
* **Reminder Notifications for Bookings**: Enable reminder notifications to keep users informed about their upcoming appointments.
* **Remove or Ignore Old Bookings**: Automatically remove or ignore old bookings to improve the speed of your **booking form**.
* **SagePay Payment Gateway**: Support for SagePay payments for secure online transactions.
* **SagePayments Payment Gateway**: Add support for SagePayments for flexible payment options.
* **SalesForce**: Create SalesForce leads with the submitted information for effective customer relationship management.
* **Checks Price on the Server Side**: Implement server-side verification of the booking price for added security.
* **Shared Availability Between Calendars**: Share booked times between calendars to prevent double bookings.
* **Skrill Payments Integration**: Support for Skrill payments for additional payment flexibility.
* **Status Modification Emails**: Define emails to be sent when the booking status changes, keeping users informed.
* **Schedule & Status Contents Customization**: Customize the contents displayed on the schedule calendar and the status colors for each form.
* **Stripe**: Add support for Stripe payments for secure online transactions.
* **iDeal TargetPay**: Support for iDeal payments via TargetPay for convenient transactions.
* **Twilio SMS Notifications for Bookings**: Enable Twilio SMS notifications to keep users updated on their bookings.
* **Zoom.us Meetings Integration**: Automatically create a Zoom.us meeting for the booked time, enhancing virtual appointment booking.
* **User Calendar Creation**: Create and assign a calendar for each new registered user for personalized scheduling.
* **WorldPay Payment Gateway**: Support for WorldPay payments for secure online transactions.
* **WooCommerce**: Integrate your **booking forms** with WooCommerce products for seamless e-commerce functionality.
* **Limit the Number of Appointments per User**: Set limits on the number of appointments


== Other Notes ==

= The Troubleshoot Area =

Use the troubleshot if you are having problems with special or non-latin characters. In most cases changing the charset to UTF-8 through the option available for that in the troubleshot area will solve the problem.

You can also use this area to change the script load method if the booking calendar isn't appearing in the public website.
 
== Screenshots ==

1. Booking form with quantity fields.
2. Simple booking form.
3. Publish form location in the new Gutemberg editor.
4. Calendar configuration.
5. Usage / Stats area
6. Bookings list
7. Email reports
8. Managing forms
9. Publishing the form with the new editor Gutemberg block

== Changelog ==

= 1.0.03 =
* First version published

= 1.0.04 =
* Improved CSV exports character encoding

= 1.0.05 =
* Fixed issue in quantity management

= 1.0.06 =
* Fixed special dates edition bug
* Improved bookings schedule
* Redirect / confirmation page now supports booking parameters

= 1.0.07 =
* Fixed bug in availability edition

= 1.0.08 =
* Fixed bug in special dates edition

= 1.0.09 =
* Improved translations

= 1.0.10 =
* Fixed bug in form edition

= 1.0.11 =
* Support to booking status

= 1.0.12 =
* Better CSS customization options

= 1.0.14 =
* Clone calendar feature

= 1.0.15 =
* Removed use of CURL

= 1.0.16 =
* Integration with Elementor
* New visual calendar for the schedule view
* Feature for adding bookings from dashboard

= 1.0.17 =
* Removed min/max date restriction for admin bookings
* Fixed available dates definition bug

= 1.0.18 =
* Increased limit of max slots
* Improved language translations support

= 1.0.19 =
* New feature for min available date in hours
* Improved form builder styles
* Added SSL detection

= 1.0.20 =
* Fixed conflict with Visual Composer

= 1.0.21 =
* Fixed bug in special dates

= 1.0.22 =
* Improvements to user permissions section

= 1.0.23 =
* Date format fix

= 1.0.24 =
* Fixed compatibility issue with PHP 7.2+

= 1.0.25 =
* Fixed conflict with lazy loading feature of Jetpack

= 1.0.26 =
* Fixed conflict with Yoast SEO

= 1.0.27 =
* Fixed captcha bug

= 1.0.28 =
* Compatible with WordPress 5.2

= 1.0.29 =
* Added features for adding custom colors to slots depending of booked spaces

= 1.0.30 =
* Language support improvements

= 1.0.31 =
* Date format improvements

= 1.0.32 =
* Fixed bug in iconv function

= 1.0.33 =
* Compatible with Google Translate

= 1.0.34 =
* Update for compatibility with WordPress 5.2

= 1.0.35 =
* iCal end time correction

= 1.0.36 =
* Code improvements

= 1.0.37 =
* Added nonce validation to settings options

= 1.0.38 =
* iCal link improvement

= 1.0.39 =
* Multiple code improvements

= 1.0.40 =
* Fix to captcha image and table encoding

= 1.0.41 =
* Update to reports

= 1.0.42 =
* Fixed bug in date filters

= 1.0.43 =
* Fixed bug max date filter

= 1.0.44 =
* Fixed conflict with autoptimize

= 1.0.45 =
* Fixed conflict with Elementor add-ons

= 1.0.46 =
* New dashboard list add-on

= 1.0.47 =
* New feature for using 12/24 hour format (military / non-military time)

= 1.0.48 =
* Fix to 12 hours time format

= 1.0.49 =
* Feature for highligthing specific dates

= 1.0.50 =
* Compatible with WordPress 5.3

= 1.0.53 =
* Fixed conflict with javascript minify plugins

= 1.0.54 =
* Fixed bug in exported CSV filenames

= 1.0.55 =
* New feature for dealing capacity in booking form

= 1.0.56 =
* Fixed bug in reply-to email header

= 1.0.57 =
* Fixed bug in times pre-fill

= 1.0.58 =
* Fixed bugs in date formatting

= 1.0.59 =
* New tags for emails

= 1.0.60 =
* Fixed conflict with bootstrap datepicker

= 1.0.61 =
* Improved translations

= 1.0.62 =
* Improved iCal add-on
* Better price number formatting for selected times

= 1.0.63 =
* Support for multiple list in same page

= 1.0.64 =
* Multiple improvements and bug fixes

= 1.0.65 =
* Interface improvements

= 1.0.66 =
* iCal export and ics files improvements

= 1.0.67 =
* Fixed bug in invalid dates

= 1.0.68 =
* Fixed bug in working dates

= 1.0.69 =
* New hooks for conversion tracking and improved CSV

= 1.0.70 =
* Added new time intervals

= 1.0.71 =
* PHP 7.x compatibility update

= 1.0.72 =
* Fixed bug in price calculation

= 1.0.73 =
* Compatible with WordPress 5.4

= 1.0.74 =
* Improved translations
* Fixed optimization / cache conflicts

= 1.0.75 =
* Improvement to avoid conflicts with third party themes

= 1.0.76 =
* Update for Gutemberg integration

= 1.0.77 =
* Fixed bug in max-date restriction

= 1.0.78 =
* Improved load speed
* Automatic compatibility with most script optimizers

= 1.0.79 =
* Better visualization speed

= 1.0.80 =
* New translations and language improvements

= 1.0.81 =
* Automatic translation of date format

= 1.0.82 =
* Fixed bug in min-date settings

= 1.0.83 =
* Added support for up to 4 different quantity fields, example, for selecting different number of "Adults", "Children" and "Infants" for the booking

= 1.0.84 =
* Fixed bug in special dates

= 1.0.85 =
* Interface improvements, translations and new quantity feature

= 1.0.86 =
* Multiple interface improvements

= 1.0.87 =
* Optimizations

= 1.0.88 =
* Fixed bug in slots selection

= 1.0.89 =
* New feature for supporting quantity 0 in first quantity fields
* Improved multi-language support

= 1.0.90 =
* Fixed bug when no quantity is used

= 1.0.91 =
* Fixed calendar initialization bug

= 1.0.92 =
* Optional 0 quantity for first qty fields

= 1.0.93 =
* Fixed to the schedule CSV export

= 1.0.94 =
* Add multiple appointment times w/ price structure

= 1.0.95 =
* Removed console log debug line

= 1.0.96 =
* Fixed backward compatibility bug

= 1.0.97 =
* Compatible with WordPress 5.5

= 1.0.98 =
* Fixed bug in show used slots feature

= 1.0.99 =
* Translation and interface improvements

= 1.1.05 =
* Fixed bug related to the current selection

= 1.1.06 =
* Fixed availability verification bug

= 1.1.07 =
* jQuery compatibility update

= 1.1.08 =
* jQuery deprecated code update

= 1.1.09 =
* Add-ons update

= 1.1.10 =
* Fix issue with mutliple forms in same page

= 1.1.11 =
* Improvemets to min and max date settings

= 1.1.12 =
* Enhanced Max-date rule

= 1.1.14 =
* Fixed conflict with optimization plugins
* New tag %final_price_short% for the emails

= 1.1.15 =
* Improved timeslot price calculation

= 1.1.16 =
* New design theme: Modern responsive with times aligned to the right side of the calendar

= 1.1.17 =
* Fix to min-date time formats

= 1.1.18 =
* Fixed bug related to the min date and max date features

= 1.1.19 =
* Non-military time settings: 12 / 24 hours formating for %app_slot_N% tags

= 1.1.20 =
* Improvement for multiple calendars in the same booking form

= 1.1.21 =
* Fixed price calculation issues

= 1.1.22 =
* Compatibility update for WordPress 5.6

= 1.1.23 =
* Modern theme update

= 1.1.24 =
* Improver time slot selection behavior

= 1.1.25 =
* Better responsive layout for iPhone

= 1.1.26 =
* Calendar visualization improvements

= 1.1.27 =
* CVS Export update for special chars

= 1.1.28 =
* New calendar design theme

= 1.1.29 =
* Event management improved

= 1.1.30 =
* Improve to the cost calculations

= 1.1.31 =
* Improved styles

= 1.1.32 =
* Schedule Calendar View improvements

= 1.1.33 =
* Compatibility with WordPress 5.7

= 1.1.34 =
* Option to ignore field validation in backend

= 1.1.35 =
* Improved script initialization

= 1.1.36 =
* New translations

= 1.1.37 =
* Fixed validation issue 

= 1.1.38 =
* CSS fixes

= 1.1.39 =
* Visualization improvements

= 1.1.40 =
* PHP 8.x and language updates

= 1.1.41 =
* PHP 8.x compatibility fix

= 1.1.42 =
* CSS Improvements

= 1.1.43 =
* Corrected styles

= 1.1.44 =
* Translation updates

= 1.1.45 =
* Support for additional translations

= 1.1.46 =
* Fixed conflict with some translations

= 1.1.47 =
* Improved email validation

= 1.1.48 =
* Translations update

= 1.1.49 =
* Better WPML integration

= 1.1.50 =
* Compatible with WordPress 5.8

= 1.1.51 =
* Schedule calendar update

= 1.1.52 =
* Accessibility improvements

= 1.1.53 =
* Min/max date settings defaults for admin

= 1.1.54 =
* Time selection interface improvement

= 1.1.55 =
* Events updated

= 1.1.56 =
* Fix to CSV export

= 1.1.57 =
* Fixed form setup conflict

= 1.1.58 =
* CSV/Excel Export feature update

= 1.1.59 =
* Support for new script events

= 1.1.60 =
* Translation updates

= 1.1.61 =
* New form layout

= 1.1.62 =
* Compatible with WordPress 5.9

= 1.1.63 =
* Multiple data sanitization

= 1.1.64 =
* Code improvements

= 1.1.65 =
* Removal of code blocks not longer used

= 1.1.66 =
* CSV Export fix

= 1.1.67 =
* Database update

= 1.1.68 =
* iCal add-on update

= 1.1.69 =
* Misc improvements

= 1.1.70 =
* Compatible with WordPress 6.0

= 1.1.71 =
* Validation fix

= 1.1.72 =
* Fixed status update action

= 1.1.73 =
* Avoid conflict with 3rd party calendar scripts

= 1.1.74 =
* Improved admin area

= 1.1.75 =
* Code improvements

= 1.1.76 =
* Fix to list shortcode

= 1.1.77 =
* Feedback panel update

= 1.1.78 =
* Better captcha

= 1.1.79 =
* Compatible with WP 6.1

= 1.1.80 =
* Language and interface updates

= 1.1.81 =
* PHP 8 updates

= 1.1.82 =
* Form builder updates

= 1.1.83 =
* Permissions adjustments

= 1.1.84 =
* PHP 8 update

= 1.1.85 =
* PHP 8 update

= 1.1.86 =
* iCal add-on update

= 1.1.87 =
* iCal export update

= 1.1.88 =
* Compatible with WordPress 6.2

= 1.1.89 =
* Fix to app field tags

= 1.1.90 =
* Price calculation update1

= 1.1.91 =
* WP 6.2 update

= 1.1.92 =
* PHP 8 fix

= 1.1.93 =
* Export CSV fix

= 1.1.94 =
* Price calculation update

= 1.1.95 =
* Compatible with WordPress 6.3

= 1.1.96 =
* CSV fix

= 1.1.97 =
* Booking list improved

= 1.1.98 =
* Compatible with WordPress 6.4

= 1.1.99 =
* Fixed pagination

= 1.2.01 =
* Dashboard add-on update

= 1.2.02 =
* New tags supported

= 1.2.03 =
* Edition interface improved

= 1.2.04 =
* Reports update

= 1.2.05 =
* Tags removed: form,meeting,appointment,schedule,scheduling,event,

= 1.2.06 =
* Server side price calculation

= 1.2.07 =
* Compatible with WP 6.5
* Updated server side price calculation

= 1.2.08 =
* CSV Export fix

= 1.2.09 =
* CSV Export schedule fix

= 1.2.10 =
* Fixed Schedule Calendar View CSS

= 1.2.11 =
* Improved sanitization

= 1.2.12 =
* Fix to Schedule calendar data load

= 1.2.13 =
* New translations

= 1.2.15 =
* PHP 8 update

= 1.2.16 =
* Best add-ons interface

= 1.2.17 =
* Improved messages list

= 1.2.18 =
* Internationalization 

= 1.2.19 =
* Language updates 

= 1.2.20 =
* Price calculation fix

= 1.2.21 =
* Typos / translation

= 1.2.22 =
* Captcha update

= 1.2.23 =
* Fix JS conflict

= 1.2.24 =
* Translations and compatibility with older WP

= 1.2.25 =
* Compatible with WP 6.8

= 1.2.26 =
* WP 6.8 reload issue fix

= 1.2.27 =
* iCal export

= 1.2.28 =
* New add-ons and improved interface

= 1.2.29 =
* WP 6.8 fix

= 1.2.30 =
* Closing / previous output

= 1.2.31 =
* Admin actions improvements

= 1.2.32 =
* Doc updates

== Upgrade Notice ==

= 1.2.32 =
* Doc updates