# Veralidity_SecureVault for Adobe Commerce Magento 2 OpenSource

## Overview
`Veralidity_SecureVault` is a Magento 2 extension designed for encrypting customer data to enhance data security in e-commerce. This proof of concept aims to provide unparalleled data protection, ensuring customer privacy and security.

## Key Features
- **Robust Encryption**: Encrypts almost all customer data during account creation and editing. Aims to extend this to all customer-related data.
- **Selective Decryption**: Data is decrypted only when necessary, such as during order processing or account editing.
- **Compatibility**: Works seamlessly with Magento's core and third-party extensions, using a custom MySQL PDO Adapter for data encryption and decryption.
- **Focused Data Protection**: Currently encrypts data in Customer Information and Customer Addresses tables, with plans to expand to other relevant tables.

## Goals and Progress
- **Ultimate Data Security**: The end goal is to create a shopping experience where customer data is highly secure, minimizing the impact of potential data breaches.
- **Anonymous Shopping Experience**: Aims to create 'ghost accounts' where data remains encrypted except when required.
- **Ongoing Development**: Encryption of email and country directory code is under consideration. Decryption functionality is partially implemented and is a work in progress.
- **Future Enhancements**: Plans include encrypting data in additional tables (e.g., Sales Quote tables) and developing a data migration script for existing customer data.

## Technical Approach
- **Encryption Method**: Utilizes standard Magento encryption methods, leveraging Magento's encryption key.
- **Database Interaction**: Custom MySQL PDO Adapter handles the encryption and decryption of data.
