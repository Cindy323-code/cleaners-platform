# Cleaners Platform - Requirements Specification (v0.1)
#  Requirements Specification Document

## 1. Cover Page

**Project Title**:  C2C Freelance Home Cleaners Platform  
**Team Name**: Chiikawa  
**Team Members**: [List all team members with student IDs]  
**Version**: v1.0  
**Date**: April 2025  
**Subject**: CSIT314 Software Development Methodologies Group Project  
**Institution**: University of Wollongong SIM S2 2025  

---

## 2. Table of Contents

1. Cover Page  
2. Table of Contents  
3. Introduction  
4. Stakeholders & User Roles  
5. User Stories  
6. Functional Requirements  
7. Use Case Diagrams & Descriptions  
8. BCE Design & Sequence Diagrams  
9. Data Model Design  
10. User Interface Design  
11. Test Plan  
12. Agile Practice Evidence  
13. Additional Components (Meetings, CI/CD, Ethics, Data-Driven Feature)

---

## 3. Introduction

### 3.1 Project Background

cleaner-platform is a web-based platform designed to connect **home owners** in need of cleaning services with **freelance cleaners** offering those services. The platform supports **C2C (Customer-to-Customer)** service matching, providing functionalities for service listing, booking, evaluation, and reporting.

The project is developed as part of the CSIT314 Software Development Methodologies module. Development follows the **Scrum agile methodology**, and the system architecture adheres to the **BCE (Boundary-Control-Entity)** design framework.

### 3.2 Project Objectives

- Enable home owners to search for, view, and manage their preferred cleaners  
- Allow cleaners to manage service listings and track engagement  
- Equip platform managers with service category management and reporting tools  
- Apply object-oriented programming, test-driven development (TDD), and CI/CD principles  
- Use agile project management tools (e.g., Taiga) to track development progress across sprints  

---

## 4. Stakeholders & User Roles

The system identifies the following primary user roles:

### 4.1 Home Owner
- Register and log in/out  
- Search for cleaners by service type, location, or rating  
- View cleaner profiles and service details  
- Save cleaners to a shortlist (favorites)  
- View historical cleaning service usage, with filters  

### 4.2 Cleaner
- Register and log in/out  
- Create, edit, and delete their own services  
- Track views and times shortlisted  
- View confirmed jobs and filter by date or service type  

### 4.3 Platform Manager
- Log in/out  
- View, create, edit, and delete service categories  
- Generate reports (daily, weekly, monthly)  
- Oversee service trends and platform activity  

### 4.4 User Admin
- View and manage user accounts  
- Edit or suspend user profiles  
- Moderate and maintain user-related records


### 5. User Stories 

This section lists all 42 user stories identified in the cleaner platform, categorized by user role and arranged in sequential order based on their unique story ID.

---

###  User Admin

**#1** As a user admin, I want to create a user account so that new users can join the system.  
**#2** As a user admin, I want to view the details of a user account so that I can verify the information or perform necessary checks.  
**#3** As a user admin, I want to update user account details so that the information remains accurate and up-to-date.  
**#4** As a user admin, I want to suspend a user account so that the user can no longer log in if they violate platform rules.  
**#5** As a user admin, I want to search for a user account by name, role, or email so that I can quickly locate their account.  
**#6** As a user admin, I want to create a user profile so that the user’s services or preferences can be displayed in the system.  
**#7** As a user admin, I want to view a user profile so that I can check the information provided.  
**#8** As a user admin, I want to update a user profile so that the latest information and availability are shown.  
**#9** As a user admin, I want to suspend a user profile temporarily so that others cannot contact the user while they’re unavailable.  
**#10** As a user admin, I want to search for a user profile so that I can find potential cleaners or clients to connect with.  
**#11** As a user admin, I want to log in to the system so that I can access my dashboard and services.  
**#12** As a user admin, I want to log out of the system so that my session is safely ended.

---

###  Cleaner

**#13** As a cleaner, I want to create a new cleaning service so that home owners can view and request my services.  
**#14** As a cleaner, I want to view the list and details of my cleaning services so that I can monitor what I am offering.  
**#15** As a cleaner, I want to update a cleaning service details so that my information is accurate and competitive.  
**#16** As a cleaner, I want to delete a cleaning service that I no longer provide so that home owners won't see outdated offerings.  
**#17** As a cleaner, I want to search and filter my cleaning services (e.g., by name, type, availability) so that I can easily find and manage specific services.  
**#18** As a cleaner, I want to log in to the system so that I can access my dashboard and manage my cleaning services.  
**#19** As a cleaner, I want to log out from the system so that I can safely end my session.  
**#27** As a cleaner, I want to view how many times my cleaning service has been viewed so that I can understand how much interest my service is generating.  
**#28** As a cleaner, I want to view how many times my cleaning service has been added to a shortlist so that I can evaluate how appealing my services are to home owners.  
**#29** As a cleaner, I want to search my history of confirmed matches by service type and date range so that I can track my work and earnings more effectively.  
**#30** As a cleaner, I want to view the details of my past confirmed service matches so that I can review my job history and client interactions.

---

###  Home Owner

**#20** As a home owner, I want to search for available cleaners based on criteria (e.g., service type, rating, availability) so that I can find suitable cleaners for my needs.  
**#21** As a home owner, I want to view detailed profiles of cleaners so that I can evaluate their services, experience, and ratings.  
**#22** As a home owner, I want to save a cleaner to my shortlist (favourite list) so that I can easily review and book them later.  
**#23** As a home owner, I want to view the list of cleaners I’ve saved so that I can quickly access and compare them.  
**#24** As a home owner, I want to search my shortlist by cleaner name, service type, or location so that I can efficiently manage my preferred cleaners.  
**#25** As a home owner, I want to log in to the system so that I can manage my bookings and preferences.  
**#26** As a home owner, I want to log out from the system so that my personal data and session are secure.  
**#31** As a home owner, I want to search my history of past cleaning services by service type and date range so that I can easily find past services for reference or rebooking.  
**#32** As a home owner, I want to view the details of all my past cleaning services so that I can track my service usage and evaluate cleaners I’ve worked with.

---

###  Platform Manager

**#33** As a platform manager, I want to create new service categories for cleaning services so that cleaners can classify their services accurately.  
**#34** As a platform manager, I want to view the list of existing service categories so that I can monitor how services are organized.  
**#35** As a platform manager, I want to update a service category’s name or details so that I can keep the classification up-to-date.  
**#36** As a platform manager, I want to delete a service category that is no longer used so that the platform remains clean and relevant.  
**#37** As a platform manager, I want to search for a specific service category by keyword so that I can quickly locate and manage it.  
**#38** As a platform manager, I want to generate and view a daily report so that I can monitor platform activity on a daily basis.  
**#39** As a platform manager, I want to generate and view a weekly report so that I can track trends and changes over the week.  
**#40** As a platform manager, I want to generate and view a monthly report so that I can analyze performance and plan improvements.  
**#41** As a platform manager, I want to log in to the system so that I can access management tools and reports.  
**#42** As a platform manager, I want to log out from the system so that I can securely end my session.

## 6. Functional Requirements

This section outlines the functional requirements of the cleaner platform, derived from the user stories. Each requirement includes its associated task list to guide system implementation.

---

###  User Admin

**#1 Create user account**
- Display user registration form
- Validate input fields (name, email, password, etc.)
- Store account in database

**#2 View user account details**
- Search for user by ID
- Display user’s account info

**#3 Update user account details**
- Edit name, email, role, etc.
- Validate and save updated info

**#4 Suspend user account**
- Click “Suspend” on user profile
- Set user status to “inactive”
- Prevent login if suspended

**#5 Search for user account**
- Input name, email or role as keyword
- Display matching results in list

**#6 Create user profile**
- Add profile details (DOB, address, etc.)
- Link profile to account

**#7 View user profile**
- Search profile by ID
- Display full profile details

**#8 Update user profile**
- Edit profile fields (e.g., phone, availability)
- Save changes

**#9 Temporarily suspend user profile**
- Mark profile as “unavailable”
- Hide profile from search

**#10 Search for user profile**
- Filter by keyword, role, or status
- Return profile list

**#11 Log in**
- Authenticate email and password
- Redirect to dashboard upon success

**#12 Log out**
- End session and redirect to login page

---

###  Cleaner

**#13 Create cleaning service**
- Input service name, type, description, price
- Save service to database

**#14 View list of cleaning services**
- Show all services by current cleaner
- Include service details in table

**#15 Update cleaning service**
- Edit title, price, or description
- Save updated record

**#16 Delete cleaning service**
- Click “Delete” for a listed service
- Remove from database

**#17 Search/filter own services**
- Filter by name, type, availability
- Display matching results

**#18 Log in**
- Authenticate and enter cleaner dashboard

**#19 Log out**
- End session and return to login

**#27 View number of service views**
- Show view counter per service

**#28 View times added to shortlist**
- Display how many users shortlisted a service

**#29 Search confirmed match history**
- Filter by service type and date
- Show matches with summary

**#30 View past confirmed match details**
- Select a match and view full info:
  - Home owner name, date, payment

---

###  Home Owner

**#20 Search for cleaners**
- Input filter criteria: type, rating, availability
- Display list of available cleaners

**#21 View cleaner profiles**
- Click on cleaner name to open profile
- Show description, ratings, service list

**#22 Save cleaner to shortlist**
- Click “Add to shortlist” on cleaner profile
- Store link in user's favorite list

**#23 View shortlist**
- Display saved cleaners in a grid/list

**#24 Search within shortlist**
- Filter by cleaner name, type, or location

**#25 Log in**
- Authenticate and enter home owner dashboard

**#26 Log out**
- End session and clear user data

**#31 Search past cleaning history**
- Filter by service type and date range
- Show matching historical bookings

**#32 View full past cleaning details**
- Display all completed services with:
  - Cleaner name, date, service type, feedback

---

###  Platform Manager

**#33 Create new service category**
- Input name and description
- Save category to database

**#34 View all service categories**
- Display full list of existing categories

**#35 Update service category**
- Edit name or description
- Save changes to database

**#36 Delete service category**
- Click “Delete” on a category
- Confirm and remove entry

**#37 Search categories by keyword**
- Input keyword to search by name or description

**#38 Generate daily report**
- Select date (default: today)
- Show summary: logins, bookings, earnings, etc.

**#39 Generate weekly report**
- Select week range
- Display 7-day summary statistics

**#40 Generate monthly report**
- Select month/year
- Show trend charts and aggregated data

**#41 Log in**
- Authenticate as platform manager
- Access admin tools and reporting dashboard

**#42 Log out**
- Securely end session and return to login screen


---

