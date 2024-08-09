# Student Board WordPress Theme Documentation

## Introduction
Welcome to the Student Board WordPress theme documentation. This theme is designed to help schools quickly and easily create their own student service websites. Student Board is an improvement based on the following theme:
- **Theme URL**: [Variations](https://en-au.wordpress.org/themes/variations/)
- **Author**: Tyler Moore
- **Author URL**: [Tyler Moore](https://en-au.wordpress.org/themes/author/conutant/)
- **Version**: 4.3.2
- **Required PHP Version**: 5.7
This guide provides detailed insights into the theme structure and functionality, aiming to help developers and contributors easily understand it.

## Theme Overview
Student Board is a WordPress theme with a clean design, developed for educational institutions and schools, dedicated to providing an efficient and intuitive student service website solution. Through the content in this theme, anyone can quickly and easily create a beautiful and fully functional service website without any web development skills or knowledge. The entire theme has rich modules available to developers, helping them create student service websites that match their ideas:
- **Responsive Design**: Ensures the website displays well on various devices (including desktop computers, tablets, and mobile phones).
- **Easy to Customize**: Through the WordPress customizer, you can easily adjust the theme's colors, fonts, and layout to adapt to different schools' branding needs.
- **Student Service Modules**: Includes modules for schools to showcase special services for students, activities, etc., helping schools organize and display important information.
- **Event Management**: Integrated event management functionality for easy creation, management, and display of campus activities and academic events.
- **Contact Form**: Built-in contact form for easy communication and consultation between students and the school.
- **Search Functionality**: Provides powerful search functionality to help users quickly find needed information.
- **Compatible with Multiple Plugins**: Compatible with common WordPress plugins like WP Form 7, The Event, etc., optimizing your performance.

## Features (Please ensure you have downloaded the corresponding plugins: Event Tickets, Events Shortcodes For The Events Calendar, The Events Calendar. For detailed information, please refer to [site.md](https://github.com/onegeniuslykdat/CP5637_GROUP2_STUDETBOARDWEBSITE/blob/main/documents/site.md#plugins))
- **Responsive Design**: Ensures the best user experience on various devices.
- **Customizable Color Scheme**: Easily modify the main color scheme through the WordPress customizer.
- **Tool Areas**:
  - Events search
  - Events ticketing and check-in functionality
- **Custom Page Templates**:
  - Full-width template: Displays normally at any size
  - Events template
  - Easily customize the website using the WordPress customizer, including site title, tagline, and logo.

## Files That May Need Editing
- **style.css**: Customize overall style, including colors, fonts, and layout.
- **header.php and footer.php**: Adjust the composition and content of the header and footer sections.
- **functions.php**: Add or modify theme functionality, including custom scripts.

## Design Decisions
### Layout 
- **Overall Layout**: Use CSS Grid for a responsive design. 
- **Page Layout**: 
   - **Home Page**: Two-column layout. 
   - **Contact Page**: Single-column layout. 
   - **Resource Page**: Grid layout for individual resources. 
### Images 
- **Recommended Sizes**: 
- **Full-width banners**: 1920x1080px. 
- **Content images**: 800x600px. 
- **Icons**: 64x64px. 
- **Format**: Use `.webp` for better compression and quality. 
### Heading Levels Adjustments 
- **Home Page**: 
    - **H2**: Initiatives powered by the JCU Brisbane Student Board 
    - **H3**: Support caring for your children 
    - **H3**: Free fruit delivered every week 
    - **H3**: Events and activities 
    - **H3**: Free coffee and hot chocolate 
    - **H1**: Upcoming Events 
    - **H2**: Contact Us 
- **Contact Page**: 
    - **H2**: Tell Us What You Think.
    - **H2**: We'd Love to Hear You! 
    - **H2**: Connect with Us Anytime! 
    - **H3**: Our Location 
    - **H3**: Reach Out 
    - **H3**: Office Hours 
- **Resource Page**: 
   - **H2**: Student Association 
   - **H2**: Accommodation 
   - **H2**: Library 
   - **H3**: Our Commitment 
   - **H2**: Empowering Student Success Through Exceptional Support Services

## Development Process
- **Local Development**: Use XAMPP to develop and test the theme in a local environment, ensuring all functions run correctly in a secure environment before deploying the website to our online hosting platform (supported by CloudAccess.net). Create a Child Theme for each new version to protect custom modifications during updates.
- **Version Control**: Use Git for version control. Ensure all code changes have detailed commit messages for easy tracking and rollback of historical versions, ensuring code reliability and traceability.
- **Theme Updates**: Regularly check and apply WordPress core and plugin updates to ensure the theme remains compatible with the latest version of WordPress, while conducting necessary compatibility tests.

## Support and Contact
If you have any questions or need help, please contact:
JCUB Student Board - info@jcubsa.edu.au
Zhangnan Fan - Zhangnan.Fan@my.jcu.edu.au


