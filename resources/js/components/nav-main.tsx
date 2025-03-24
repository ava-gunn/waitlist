import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChevronDown, ChevronRight } from 'lucide-react';
import { useState } from 'react';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();
    const [expandedItems, setExpandedItems] = useState<Record<string, boolean>>({});

    const toggleExpanded = (title: string, e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setExpandedItems(prev => ({
            ...prev,
            [title]: !prev[title]
        }));
    };

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>Platform</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => (
                    <SidebarMenuItem key={item.title}>
                        {item.children && item.children.length > 0 ? (
                            <>
                                <SidebarMenuButton 
                                    asChild
                                    isActive={page.url === item.url || page.url.startsWith(item.url + '/')}
                                >
                                    <Link href={item.url} prefetch>
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                        {/* Chevron icon with click handler for dropdown toggle */}
                                        <span 
                                            className="ml-auto cursor-pointer"
                                            onClick={(e) => toggleExpanded(item.title, e)}
                                        >
                                            {expandedItems[item.title] ? 
                                                <ChevronDown className="h-4 w-4" /> : 
                                                <ChevronRight className="h-4 w-4" />
                                            }
                                        </span>
                                    </Link>
                                </SidebarMenuButton>
                                
                                {expandedItems[item.title] && (
                                    <SidebarMenuSub>
                                        {item.children.map((child: NavItem) => (
                                            <SidebarMenuSubItem key={child.title}>
                                                <SidebarMenuSubButton 
                                                    asChild 
                                                    isActive={page.url === child.url}
                                                >
                                                    <Link href={child.url} prefetch>
                                                        <span>{child.title}</span>
                                                    </Link>
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        ))}
                                    </SidebarMenuSub>
                                )}
                            </>
                        ) : (
                            <SidebarMenuButton asChild isActive={item.url === page.url}>
                                <Link href={item.url} prefetch>
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        )}
                    </SidebarMenuItem>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
