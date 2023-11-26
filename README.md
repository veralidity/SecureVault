
Veralidity_SecureVault for Adobe Commerce Magento 2 OpenSource

This extension is a proof of concept for encrypting customer data as much as possible when a customer creates an account or edits their account.

The end goal of this functionality is to create unparalleled data security for storing customer account information. All identifiable customer information is encrypted so if theres a data breach the impact won't be as significant for customers or for the company who's data was breached.

The functionality in turn creates an almost annonymous shopping experience with ghost accounts where data is only decrypted on a need to know basis like when an administrator is processing orders, or you need to edit your account information.

Currently this extension encrypts all data with the exception of the customers email and the customers country directory code, though with enough work I'm confident that these two can also be encrypted as well.

The idea is to encrypt the data at the very last moment before being saved in the database and decrypt the data at point of database exit so that this functionality does not conflict with any other core extensions or 3rd party extensions so a new MySQL PDO Adapter is used that handles the encryption and decription of data. Standard Magento encryption methods are utilized using Magento's encryption key.

There are methods to decrypt the data at point of exit from the database however this is not fully functional yet and most data shows as being encrypted when you go to edit a customers information such as name or a customer address. Encrypted data shows in Magento admin as well. Some data is decrypted, not all. This is a work in progress.

The database tables that store Customer Information and Customer Addresses are the only tables with stored encrypted data so far and more work needs to be completed so that every database table that stores customer data is also storing encrypted customer data such as Sales Quote tables, etc.

Once this is completely working we will want to have a data migration script for existing customer data so that all existing customer data stored in the database will also be encrypted alongside any new customers.
