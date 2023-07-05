## Bkash Payment Gateway
bKash Limited (bKash) is a Bank-led Mobile Financial Service Provider in Bangladesh operating under the license and approval of the Central Bank (Bangladesh Bank) as a subsidiary of BRAC Bank Limited. bKash provides safe, convenient and easy ways to make payments and money transfer services via mobile phones to both the unbanked and the banked people of Bangladesh.

bKash is the fastest and safest medium of financial transaction. bKash users can deposit money into their mobile accounts and then access a range of services. It makes your life simple with Send Money, Add Money, Pay Bill, Mobile Recharge, Payment and many more services.


### Features
- Easy Integration
- Personalised payment experience
- Add vat or surcharge
- Secure OTP and PIN based access

### Configuration

You can install this plugin from Moodle plugins directory or can download from Github.

You can download zip file and install or you can put file under payment/gateway as Bkash.

### Plugin Global Settings

After installing the plugin you'll automatically redirected to this page.

![bkash settings](https://github.com/shadman-ahmed-bs23/laravel-docker-sample/assets/72008371/cf9dc7f0-8394-48e6-b7fe-0bc676f24f3e)

## Configuring the Bkash Payment Gateway:
### Step: 1
```
Dashboard / Site administration / Plugins / Payment gateways / Manage payment gateways
```
![payment gateway settings](https://github.com/shadman-ahmed-bs23/laravel-docker-sample/assets/72008371/b2e0890d-309b-4a54-b649-b4ed8b2c256d)

Enable Bkash plugin 

![enable shurjopay](https://github.com/shadman-ahmed-bs23/laravel-docker-sample/assets/72008371/74ff63d0-6703-4a70-abb5-fb6a4b3d4100)


### Step: 2
At first create a payment account, from the following path:
```
Dashboard->Site Administration->General->Payment accounts
```
![payment_accounts](https://github.com/shadman-ahmed-bs23/laravel-docker-sample/assets/72008371/e497850b-8465-4b63-a2f2-726a37d6babb)

After creating a payment account, go to the Bkash settings and fill in the required data: 

```
Dashboard->Site Administration->Plugins->Payment Gateways->Bkash settings
```
![bkash_options](https://github.com/shadman-ahmed-bs23/laravel-docker-sample/assets/72008371/d70dd96e-e13b-4350-afe1-257b1d1bc49d)

![bkash config](https://github.com/shadman-ahmed-bs23/laravel-docker-sample/assets/72008371/8d56a07a-6da0-484f-ba23-99696635c715)

- Insert credentials provided by bkash for sandbox.
- Click the "save changes" button to save the information

### Step: 3

Go to the Manage Enrolment Plugins section from the site administration
```
Dashboard->Site Administration->Plugins->Enrolments->Manage Enrol Plugins
```

![image](https://user-images.githubusercontent.com/97436713/153135098-3492f3d1-9dc6-401d-81b1-ad86f6f01494.png)

Enable Enrolment on payment by clicking the eye icon.

## Enrolment Settings for Course:

Now click on the course page and add an enrolment method Enrolment of Payment.

![image](https://user-images.githubusercontent.com/97436713/153138641-93f67f96-9bc1-44bf-afbd-8641b0bd8821.png)

and fill up this form below to set the amount of money and currency for the course payment

![image](https://user-images.githubusercontent.com/40598386/190346195-ca970aa3-4114-4056-9225-7f6e875d0c17.png)

This is how it looks like from a student's perspective:

![image](https://user-images.githubusercontent.com/40598386/190346825-e237a550-b200-4dc2-a46f-e4f1153dd0d6.png)

Select the Payment Type- Bkash the surcharge is added with the course payment amount

![bkash payment modal](https://github.com/shadman-ahmed-bs23/laravel-docker-sample/assets/72008371/45f2c1d4-5fd0-4079-9872-16e1b105db1d)


If your payment is successful then you'll be enrolled in the course.

## Author
- [Brain Station 23 Ltd.](https://brainstation-23.com)

## License
This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see [GNU License](http://www.gnu.org/licenses/).


