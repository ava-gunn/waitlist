import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { AlertCircle, ArrowRight, CalendarDays, Clock, ExternalLink, PlusCircle } from 'lucide-react';

// Define the Project interface inline to avoid import issues
interface Project {
    id: number;
    name: string;
    subdomain: string;
    description?: string;
    logo_path?: string;
    settings?: {
        theme?: string;
        collect_name?: boolean;
        social_sharing?: boolean;
    };
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
    signups_count?: number;
}

interface DashboardProps {
    recentProjects?: {
        data: Project[];
    } | Project[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard({ recentProjects = [] }: DashboardProps) {
    // Add debugging to check what projects data is being received
    console.log('Dashboard recentProjects:', recentProjects);
    
    // Process projects data to handle both array and object with data property
    const projectsData = Array.isArray(recentProjects)
        ? recentProjects
        : recentProjects?.data || [];

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        subdomain: '',
        description: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/projects', {
            onSuccess: (page: any) => {
                if (page.props.flash?.success) {
                    // Extract the project ID from the location we were redirected to
                    // The current URL would be something like /projects/1
                    const redirectPath = window.location.pathname;
                    if (redirectPath.startsWith('/projects/')) {
                        // We're already on the project page, nothing else to do
                    }
                }
            },
            // Preserve the scroll position and don't reload the page
            preserveScroll: true,
            // Force the full page reload to ensure we go to the project page
            forceFormData: true,
        });
    };

    const handleSubdomainChange = (value: string) => {
        // Only allow lowercase alphanumeric characters and hyphens
        // Cannot start or end with a hyphen
        const formatted = value
            .toLowerCase()
            .replace(/[^a-z0-9-]/g, '')
            .replace(/^-+|-+$/g, '');
        
        setData('subdomain', formatted);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="p-6 md:p-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold">Welcome to your Waitlist Dashboard</h1>
                    <p className="text-muted-foreground mt-2">Manage your waitlists and view signup analytics</p>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Quick Project Creation</CardTitle>
                            <CardDescription>
                                Create a new waitlist project in seconds
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="quick-name">
                                        Project Name <span className="text-destructive">*</span>
                                    </Label>
                                    <Input
                                        id="quick-name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="My Awesome Project"
                                        aria-required="true"
                                        aria-invalid={errors.name ? 'true' : 'false'}
                                    />
                                    {errors.name && (
                                        <p className="text-sm text-destructive flex items-center gap-1">
                                            <AlertCircle className="size-3.5" />
                                            {errors.name}
                                        </p>
                                    )}
                                </div>
                                
                                <div className="space-y-2">
                                    <Label htmlFor="quick-subdomain">
                                        Subdomain <span className="text-destructive">*</span>
                                    </Label>
                                    <div className="flex items-center gap-0">
                                        <Input
                                            id="quick-subdomain"
                                            value={data.subdomain}
                                            onChange={(e) => handleSubdomainChange(e.target.value)}
                                            placeholder="myproject"
                                            className="rounded-r-none"
                                            aria-required="true"
                                            aria-invalid={errors.subdomain ? 'true' : 'false'}
                                        />
                                        <div className="flex h-10 items-center rounded-r-md border border-l-0 bg-muted px-3 text-sm text-muted-foreground">
                                            .{window.location.host.split('.').slice(1).join('.')}
                                        </div>
                                    </div>
                                    {errors.subdomain && (
                                        <p className="text-sm text-destructive flex items-center gap-1">
                                            <AlertCircle className="size-3.5" />
                                            {errors.subdomain}
                                        </p>
                                    )}
                                </div>

                                <div className="pt-2 flex justify-between">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? 'Creating...' : 'Create Project'}
                                    </Button>
                                    <Button variant="outline" type="button" asChild>
                                        <Link href="/projects/create" className="flex items-center gap-1">
                                            Advanced Options <ArrowRight className="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Projects</CardTitle>
                            <CardDescription>
                                Your most recent projects
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {projectsData.length > 0 ? (
                                <div className="space-y-4">
                                    {projectsData.map((project: Project) => (
                                        <div key={project.id} className="flex items-start space-x-4 rounded-md border p-3 transition-colors hover:bg-muted/50">
                                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-primary/10">
                                                <div
                                                    className="h-full w-full rounded-md bg-cover bg-center"
                                                    style={{
                                                        backgroundColor: project.settings?.theme === 'dark' ? '#1f2937' : '#f3f4f6',
                                                        backgroundImage: project.logo_path ? `url(${project.logo_path})` : undefined,
                                                    }}
                                                    aria-hidden="true"
                                                />
                                            </div>
                                            <div className="flex-1 space-y-1">
                                                <Link 
                                                    href={`/projects/${project.id}`}
                                                    className="font-medium text-foreground hover:underline flex items-center"
                                                >
                                                    {project.name}
                                                    <ExternalLink className="ml-1 size-3.5 text-muted-foreground" />
                                                </Link>
                                                <div className="text-sm text-muted-foreground">
                                                    <span className="flex items-center gap-1.5">
                                                        <CalendarDays className="size-3.5" />
                                                        Created {new Date(project.created_at || '').toLocaleDateString()}
                                                    </span>
                                                </div>
                                                {project.signups_count !== undefined && (
                                                    <div className="text-sm">
                                                        <span className="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                                            {project.signups_count} {project.signups_count === 1 ? 'signup' : 'signups'}
                                                        </span>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                    
                                    <div className="pt-2">
                                        <Button asChild variant="outline" className="w-full" size="sm">
                                            <Link href="/projects" className="flex items-center justify-center gap-1">
                                                View All Projects <ArrowRight className="size-3.5" />
                                            </Link>
                                        </Button>
                                    </div>
                                </div>
                            ) : (
                                <div className="border-sidebar-border/70 dark:border-sidebar-border relative h-[300px] flex-1 rounded-xl border">
                                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                                    <div className="absolute inset-0 flex items-center justify-center flex-col gap-4">
                                        <PlusCircle className="size-12 text-muted-foreground/50" />
                                        <p className="text-muted-foreground">No projects yet</p>
                                        <Button asChild>
                                            <Link href="/projects">View All Projects</Link>
                                        </Button>
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
