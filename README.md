# Text Notification Plugin (Telegram Version)

A simple WordPress plugin that allows site owners to send Telegram notifications when a user submits their information via a form or when they view product pages. The plugin is useful for sending immediate alerts to site owners or admins about customer activity.

## Features

- **Collect User Information**: A modal form collects user details (name and phone number) and sends a Telegram message when the form is submitted.
- **Product View Tracking**: Sends a Telegram message when a customer views a product page.
- **Dynamic Configuration**: Configure the Telegram **API token** and **chat ID** directly from the WordPress admin panel.
- **Session-Based Form Display**: The form is shown only once per session to avoid redundant data collection.
- **Admin Settings Page**: Easily manage your Telegram bot credentials in the WordPress backend.

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- A Telegram bot (use **BotFather** to create one)
- A valid **chat ID** for receiving messages

## Installation

1. **Download the Plugin**:
   - Clone or download the repository to your local computer or server.
   
2. **Upload to WordPress**:
   - Upload the plugin folder to your WordPress website’s `wp-content/plugins/` directory.

3. **Activate the Plugin**:
   - Go to the WordPress admin panel.
   - Navigate to **Plugins → Installed Plugins**.
   - Find **Text Notification Plugin (Telegram Version)** and click **Activate**.

## Configuration

1. **Create a Telegram Bot**:
   - Open Telegram and search for **BotFather**.
   - Create a new bot and get the **API Token**.

2. **Find Your Chat ID**:
   - Start a conversation with your bot.
   - Go to the URL `https://api.telegram.org/bot<your_api_token>/getUpdates` to get your **chat ID**.

3. **Set Your Telegram Credentials**:
   - Go to **Settings → Telegram Settings** in your WordPress admin panel.
   - Enter your **Telegram Bot API Token** and **Chat ID** in the fields provided.
   - Click **Save Changes**.

## Usage

- **When a User Submits Their Information**:
  - The form will collect the user’s name and phone number.
  - A Telegram message will be sent to the configured **chat ID** with the user's details.

- **When a User Views a Product Page**:
  - A message with the product name and a link to the product page is sent to your **Telegram chat**.

## Settings

- **Telegram Bot API Token**: This token is required to authenticate your bot and send messages to Telegram.
- **Telegram Chat ID**: The chat ID where messages will be sent. It can be either your personal chat or a group chat ID.

## Developer Info

- **Plugin Name**: Text Notification Plugin (Telegram Version)
- **Author**: Abhitej Vissamsetty
- **Website**: [Amigoo](https://amigoo.in/)
- **License**: GPL2

## Contributing

Feel free to fork this repository, submit issues, or create pull requests to improve the plugin.

### How to Contribute:
1. Fork the repository.
2. Clone the forked repository to your local machine.
3. Create a new branch for your feature or bug fix.
4. Make your changes and commit them.
5. Push your changes to your forked repository.
6. Open a pull request from your branch to the main repository.

## License

This plugin is licensed under the **GPL2** license. See the [LICENSE](LICENSE) file for more details.

---

If you have any questions or issues, please open an issue in the GitHub repository or contact the author via [Amigoo](https://amigoo.in/).
