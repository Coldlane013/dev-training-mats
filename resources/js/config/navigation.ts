import { type NavItem } from '../types'; 
export const HEADER_NAV_ITEMS: NavItem[] = [
    {
        title: 'Home',
        href: '/',
    },
    {
        title: 'About',
        href: '/about',
    },
    // Add more items here as needed
    // {
    //     title: 'Contact',
    //     href: '/contact',
    // }
];

export const DASHBOARD_NAV_ITEMS: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin',
    },
    {
        title: 'Tickets',
        href: '/admin/tickets',
    },
    {
        title: 'Users',
        href: '/admin/users',
    },
    // Add more items here as needed
    // {
    //     title: 'Contact',
    //     href: '/contact',
    // }
];