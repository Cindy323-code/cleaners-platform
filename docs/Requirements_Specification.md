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

---

