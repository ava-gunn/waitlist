import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { AlertCircle, ArrowRight, PlusCircle } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
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
                            <CardTitle>Recent Activity</CardTitle>
                            <CardDescription>
                                Recent signups and activity across your projects
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="border-sidebar-border/70 dark:border-sidebar-border relative h-[300px] flex-1 rounded-xl border">
                                <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                                <div className="absolute inset-0 flex items-center justify-center flex-col gap-4">
                                    <PlusCircle className="size-12 text-muted-foreground/50" />
                                    <p className="text-muted-foreground">No recent activity</p>
                                    <Button asChild>
                                        <Link href="/projects">View All Projects</Link>
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
