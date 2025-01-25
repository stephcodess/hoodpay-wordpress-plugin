# HoodPay Gateway WordPress Plugin

The HoodPay Gateway WordPress plugin allows you to seamlessly integrate HoodPay payment services into your WordPress site. Users can make payments using debit cards or cryptocurrency, and you can manage transactions directly from the WordPress admin dashboard.

## Features

- **Payment Integration**: Accept payments via HoodPay.
- **Admin Management**: View and filter transactions in the admin panel.
- **Settings**: Configure API Key and Business ID.
- **Dynamic Filters**: Filter transactions by status (e.g., PENDING, COMPLETED, etc.).
- **Shortcode**: Use a shortcode to render a payment button that redirects users to HoodPay for payment.

## Installation

1. Download the plugin ZIP file or clone the repository.
2. Go to your WordPress admin dashboard.
3. Navigate to `Plugins > Add New`.
4. Click on `Upload Plugin` and select the downloaded ZIP file.
5. Click `Install Now` and then activate the plugin.

## Configuration

1. Navigate to `Settings > HoodPay Settings`.
2. Enter your HoodPay **API Key** and **Business ID**.
3. Save the changes.

## Usage

### Adding a Payment Button

Use the `[hoodpay_payment amount="100" currency="USD"]` shortcode to render a payment button. Replace `amount` and `currency` with the desired values.

Example:

```html
[hoodpay_payment amount="150" currency="EUR"]
```

### Viewing Transactions

1. Navigate to `HoodPay Transactions` in the WordPress admin menu.
2. Use the filter dropdown to filter transactions by status (e.g., COMPLETED, FAILED).

## Code Structure

### Admin Settings Page
Allows you to configure the API Key and Business ID required to connect with HoodPay.

### Transactions Page
Fetches and displays transaction data from HoodPay, with the ability to filter by payment status.

### Payment Button
The shortcode renders a payment button that sends a request to HoodPay to create a payment and redirects the user to the payment URL.

## API Endpoints Used

### Create Payment
**Endpoint**: `POST https://api.hoodpay.io/v1/businesses/{businessId}/payments`

**Request Body**:
- `name`: Name of the payment.
- `description`: Description of the payment.
- `currency`: Currency code (e.g., USD).
- `amount`: Payment amount.
- `redirectUrl`: URL to redirect the user after payment.

**Response**:
- `url`: Payment URL to redirect the user.

### Get Transactions
**Endpoint**: `GET https://api.hoodpay.io/v1/businesses/{businessId}/payments`

**Query Parameters**:
- `status`: Filter by payment status (optional).
- `pageNumber`: Pagination (default: 1).
- `pageSize`: Number of transactions per page (default: 10).

## Development Notes

### Adding the Plugin to Your Project
- Clone the repository: `git clone https://github.com/stephcodess/hoodpay-wordpress-plugin.git`
- Make changes and push updates.

### Customization
- Modify styles in `assets/css/hoodpay.css`.
- Update JavaScript logic in `assets/js/hoodpay.js`.

## Support
For issues or feature requests, please create an issue on the [GitHub repository](https://github.com/stephcodess/hoodpay-wordpress-plugin).

## Author
**Raji Olaoluwa Segun**  
[LinkedIn](https://www.linkedin.com/in/olaoluwa-raji-14a5681b8/)  
[GitHub](https://github.com/stephcodess)

## License
This plugin is open-source and available under the [MIT License](https://opensource.org/licenses/MIT).

