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

Chiikawa is a web-based platform designed to connect **home owners** in need of cleaning services with **freelance cleaners** offering those services. The platform supports **C2C (Customer-to-Customer)** service matching, providing functionalities for service listing, booking, evaluation, and reporting.

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


---

