# Veralidity_SecureVault for Adobe Commerce Magento 2 OpenSource

## Overview
**Veralidity_SecureVault** is a pioneering proof of concept extension for Adobe Commerce Magento 2. It focuses on maximizing encryption of customer data during account creation or modification, setting a new standard in data security for e-commerce platforms.

## Objectives
- **Enhanced Data Security**: The primary goal is to offer unmatched security in storing customer account information. Encrypting identifiable customer data minimizes the impact of potential data breaches for both customers and businesses.
- **Anonymous Shopping Experience**: By creating 'ghost accounts', customer data remains encrypted and is only decrypted when necessary, such as during order processing by an administrator or when customers update their account details.

## Current Capabilities
- **Selective Data Encryption**: Currently encrypts all customer data except for email addresses and country directory codes. Future developments aim to include these as well.
- **Encryption Mechanism**: Encrypts data at the final stage before database entry and decrypts upon retrieval. This approach ensures compatibility with Magento's core and third-party extensions.
- **Utilizing Magento's Encryption Methods**: Leverages standard Magento encryption techniques using Magento's own encryption key.
- **Database Adaptation**: At present, only the tables for Customer Information and Customer Addresses store encrypted data. Plans include extending encryption to all customer-related tables, like Sales Quote tables.

## Progress and Challenges
- **Partial Functionality**: Although some data decryption is operational upon database exit, this feature is still in development. Currently, encrypted data is visible in the Magento admin panel.
- **Future Expansion**: The ultimate aim is comprehensive encryption of all customer data within the database.

## Roadmap
- **Data Migration Script**: Development of a script for migrating existing customer data to an encrypted format is planned, ensuring all customer data, new and old, is securely encrypted.

## Contributions
Your feedback, suggestions, and contributions are invaluable as we strive to enhance the capabilities of Veralidity_SecureVault. Join us in redefining data security standards in e-commerce.

*Stay tuned for upcoming updates and advancements in Veralidity_SecureVault!*
