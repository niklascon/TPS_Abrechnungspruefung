# Project Name

## Introduction

This project is a web application built with PHP, SQL and JavaScript. It follows a Model-View-Controller (MVC)
architecture to separate concerns and improve maintainability.
The application allows users to upload bills related to household expenses and analyze them to identify potentially incorrect bill items.

## Project Structure

- `src/`: Contains the main source code of the application.
    - `Controllers/`: Contains the controllers that handle HTTP requests and responses.
        - Example: `CorrectionController.php` handles the logic for correcting real estate data.
    - `Models/`: Contains the models that interact with the database.
        - Example: `RealEstate.php` represents a real estate entity and includes methods for loading and saving data.
    - `Views/`: Contains the view templates that render the HTML.
        - Example: `real_estate_view.php` renders the user interface for displaying real estate data.
- `config/`: Contains configuration files for paths, database, etc.
    - Example: `paths.php` defines constants for various directory paths used in the application.
- `tests/`: Contains unit and integration tests.
    - Example: `CorrectionControllerTest.php` includes tests for the `CorrectionController` class.
- `public/`: Contains the entry point of the application and publicly accessible files.
    - `index.php` is the main entry point for this application.
  
## Prerequisites

- PHP 7.4 or higher
- MySQL or any other SQL database
- Web server (e.g., Apache, Nginx)
  - we used xampp to create a local mySQL server and apache server: [XAMPP Download](https://www.apachefriends.org/download.html)


## Installation

1. **Clone the repository:**
   ```sh
   git clone https://github.com/n1c01/TPS/.git
   cd TPS

2. **Set up the database:**
    - set up the SQL server fitting to the config `config/database/database.php` or change the config to fit your database
    - use `config/database/init_database.sql` to create the database and tables

3. **configuring the web server:**
   - Configure your web server (e.g. Apache) to point to the `public/` directory as the document root.
     - syncing the directory was an easy way to achieve this.

4. **Access the application:**
    - Open your web browser and navigate to the host defined in your web server configuration.
      - For example, `http://localhost/TPS/public/` depending on your configuration (directory of the project folder).
    - You should see the home page of the application.

## Testing

1. **Run the tests using PHPUnit:**
   ```sh
      vendor/bin/phpunit

## Architecture

The project follows the MVC architecture, which separates the application into three main components:

- **Model:** Represents the data and business logic. Models are responsible for interacting with the database and are located in the `src/Models/` directory.
- **View:** Represents the presentation layer. Views are responsible for rendering the HTML and are located in the `src/Views/` directory.
- **Controller:** Handles user input and updates the model and view accordingly. Controllers are responsible for processing HTTP requests and are located in the `src/Controllers/` directory.

## Contributing

1. **Fork the repository:**
   - Click the "Fork" button on the repository page to create a copy of the repository in your GitHub account.

2. **Create a new branch:**
   - Create a new branch for your feature or bugfix.
   ```sh
   git checkout -b feature-branch
   
3. Make your changes:  
   - Implement your feature or bugfix in the new branch.
4. Commit your changes:  
   - Commit your changes with a descriptive commit message.
   ```sh
   git commit -am 'Add new feature'
5. Push to the branch:  
   - Push your changes to your forked repository. 
   ```sh
   git push origin feature-branch
   
6. Merge the current main into your branch to avoid conflicts:
   ```sh
   git merge main
   
7. Ensure that the tests are still running:
   ```sh
   vendor/bin/phpunit
   
8. Create a Pull Request:
   - Open a Pull Request on the original repository to merge your changes.

9. **Code Review:**
   - The project maintainers will review your changes and provide feedback.
   - Make any requested changes and push them to your forked repository.
  
## Website Explanation
1. The website aims to enable tenants to upload their rent bills and have them checked for errors. The uploaded bill will be compared with the previous year’s bill. In the future, external data, such as the weather, should also be possible. The start page is shown below.
   
<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_1.png" width="90%" />
</p>

2. Before a rental statement can be uploaded, the user must log in. If the user already has an account, they can log in on the login page with their username. In the current version, user accounts are not yet protected by passwords.

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_2.png" width="90%" />
</p>

3. If the user does not yet have an account, they can create one by clicking ‘Registrieren’ on the login page. Currently, each user is assigned a property on the website at any time, so the user must add a new property when creating their account.

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_3.png" width="90%" />
</p>

4. Once logged in, users can upload a rental statement via ‘Hochladen’ in the menu bar. Users can select a desired rental statement via ‘Durchsuchen’ or use the drag-and-drop option to do this.

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_4.png" width="90%" />
</p>

5. The system now extracts the data from the uploaded PDF. Currently, only rental invoices with a specific layout are accepted, and the PDF must be editable.
You can click the pencil icon on the current page to change specific values if they have not been correctly extracted from the document. In addition, booking types can be removed via ‘Löschen’ and added via ‘Reihe hinzufügen’. It is essential that the user gives the document a name before saving and selects the property if they have more than one. After the user clicks ‘Absenden’, the uploaded invoice is compared with the invoice from the previous year. Any significant differences are then saved in the database.

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_5.png" width="90%" />
</p>

6. Once the invoice has been added, the user is taken to the invoice overview page, which can be accessed via the navigation bar under ‘Rechnungen’. Here, the user can delete invoices.

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_6.png" width="90%" />
</p>

7. To determine whether the uploaded invoice contains anomalies, the user can navigate to ‘Prüfungsergebnisse’ in the menu bar. Invoices with anomalies are marked with a small red ‘i’ to the left of the invoice. 

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_7.png" width="90%" />
</p>

8. The user can now click the ‘i’ to obtain more detailed information. The anomalies recorded in the database are then displayed.

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_8.png" width="90%" />
</p>

9. An alternative way to view anomalies in rental statements is the ‘Expertenanalyse’, which can be accessed via the menu bar. Here, the user can select two invoices to compare. 

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_9.png" width="90%" />
</p>

10. In contrast to the ‘Prüfungsergebnisse’, the anomalies are not loaded from the database, but are generated anew. The user can now analyse the booking types for a different percentage deviation value via ‘Reihe hinzufügen’. After clicking on ‘Vergleichen’, a list of all anomalies is displayed.

<p align="center">
  <img src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/screenshot_10.png" width="90%" />
</p>

11. Future projects:
- The analysis could be expanded to include external factors
- Enable import of rental statements with different layouts
- Add OCR technology for reading rental statements
- Add passwords to the accounts
- Currently, a property can only be seen by one user, this can be designed so that multiple users have access to a property
- Invoices to be edited in the invoice overview. Also make it clear which invoice belongs to which property.
- Overall, the layout could be made more user-friendly. We have already started a user study and implemented simple solutions.
- The menu items ‘Hochladen’, ‘Prüfungsergebnisse’ and ‘Rechnungen’ could be combined into one menu item
- There are still many minor details that have not yet been finalised, such as editing properties, changing usernames, improving the design of exam results, etc.

## Database

1. Layout
   
<p align="center">
    <img width="719" alt="DatabaseLayout" src="https://github.com/n1c01/TPS_Abrechnungspruefung/blob/main/images/database_layout.png" />
</p>

- The connection between real_estate and user is of type n:m.
- A bill consists of several line items (1:n). Line items represent the lines in a bill with the type of costs and the price.
- booking types represent the type of costs. A booking type can be subtype of another booking type.
- Real_estate can be assotioated analysis_result which consist of several item_of_analysis
- text_bracket is intended to contain different warnings messages for different irregularities but is currently only used for one

2. Database Code Connection

Every table in the database has a multiple (plural form of table name) and a singular (singular form of table name) class. Those can be found in `/src/Models/database/implementation/`. 
The multiple class represents the entire table and is used for loading, adding or deleting entries. The function loadAll() is equivalent to the SQL statement `SELECT all FROM table`. To add a JOIN or WHERE clause to the statement call the corresponding functions before calling loadAll().
The singular class represents a single entry of the database and is used to get and set attributes of the database entry. To create a new entry construct an object of the class. Note that new objects or changes to an existing entry are only written to the databse when calling save()

## License
This project is licensed under the MIT License. See the LICENSE file for details.
