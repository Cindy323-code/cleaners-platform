# Cleanplatform Project Documentation

## Architectural Overview: Boundary-Controller-Entity (B-C-E)

This project follows the Boundary-Controller-Entity (B-C-E) architectural pattern. This pattern helps in organizing the codebase by separating concerns into distinct layers, enhancing maintainability, testability, and scalability.

*   **Boundary:** Handles all user interactions (input and output). It's the interface through which users or other systems interact with the application.
*   **Controller:** Acts as an intermediary, orchestrating communication between the Boundary and Entity layers. It receives requests from the Boundary, delegates tasks to the appropriate Entity objects, and then passes the results back to the Boundary.
*   **Entity:** Contains the core business logic, data structures, and rules of the application. This layer is responsible for processing data and performing the main functionalities.

## Project Structure Mapping to B-C-E

Here's how the project's directory structure aligns with the B-C-E pattern:

### 1. Boundary Layer

The Boundary layer is responsible for all user-facing interactions, including presenting information and capturing user input.

*   `public/`: This directory contains assets directly accessible by the user's browser, such as CSS files (`public/css/`), JavaScript files (if any), and images (if any). These are fundamental to rendering the user interface.
*   `boundary/`: This directory appears to contain subdirectories for different modules or sections of the user interface. Each subdirectory likely handles the presentation logic for a specific part of the application:
    *   `boundary/admin/`: User interface components for administrative tasks.
    *   `boundary/auth/`: User interface for authentication (login, registration, etc.).
    *   `boundary/category/`: User interface related to categories.
    *   `boundary/history/`: User interface for displaying history.
    *   `boundary/homeowner/`: User interface specific to homeowners.
    *   `boundary/partials/`: Reusable UI components or templates (e.g., headers, footers).
    *   `boundary/profile/`: User interface for user profiles.
    *   `boundary/report/`: User interface for generating or displaying reports.
    *   `boundary/service/`: User interface related to services.
    *   `boundary/shortlist/`: User interface for shortlists or favorites.

    **Responsibility:** These components should focus solely on rendering views, displaying data received from Controllers, and sending user actions/data to Controllers. They should not contain any business logic or direct database interactions.

### 2. Controller Layer

The Controller layer acts as the conduit between the Boundary and Entity layers, managing the flow of information.

*   `controller/`: This directory houses the controller classes. Controllers receive HTTP requests (or other forms of input from the Boundary layer), interpret them, interact with the Entity layer to perform necessary actions or retrieve data, and then select an appropriate view in the Boundary layer to present the response.

    **Responsibility:** Controllers should be lean. Their primary role is to:
    1.  Receive and validate input from the Boundary.
    2.  Call appropriate methods on Entity objects to process the request.
    3.  Prepare data for the Boundary (e.g., by selecting a view and passing data to it).
    They should not contain business logic (that belongs in Entities) or direct UI manipulation code (that belongs in Boundaries).

### 3. Entity Layer

The Entity layer is the heart of the application, containing all business logic, data manipulation, and core functionalities.

*   `Entity/`: This directory contains the entity classes. These classes represent the core data structures and business objects of the application (e.g., User, Service, Category). They encapsulate the data and the operations that can be performed on that data, including validation rules and business logic.
*   `config/`: While not strictly Entities themselves, configuration files define parameters and settings that Entities and other parts of the application rely on to function correctly. This can include database connection details, application settings, etc.
*   `bootstrap.php`: This file is typically responsible for initializing the application, setting up autoloading, establishing database connections, and preparing the environment. It plays a crucial role in enabling the Entity layer (and other layers) to function.

    **Responsibility:** Entity classes are responsible for:
    1.  Representing and managing application data.
    2.  Implementing all business rules and logic.
    3.  Interacting with the database (often through an ORM or data mapper, which might also be considered part of or closely related to the Entity layer).
    They should be independent of the Boundary and Controller layers, meaning they should not know how data is displayed or how requests are received.

## Workflow Example

1.  A user interacts with a **Boundary** component (e.g., submits a form on a webpage).
2.  The Boundary component sends the user's input to a **Controller**.
3.  The Controller validates the input and calls the relevant methods on one or more **Entity** objects.
4.  The Entity objects perform the business logic, potentially interacting with the database.
5.  The Entity objects return results (e.g., data, success/failure status) to the Controller.
6.  The Controller passes results to an appropriate Boundary component.
7.  The Boundary component displays the information to the user.

This separation ensures that changes in one layer (e.g., a UI redesign in the Boundary) have minimal impact on other layers (e.g., the business logic in the Entity). 