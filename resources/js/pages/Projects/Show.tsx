import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { type Project, type ProjectStats, type Signup } from '@/types/project';
import { Head, Link } from '@inertiajs/react';
import { BarChart3, Download, PencilLine, Plus, Trash2, Users } from 'lucide-react';
import { useState } from 'react';

interface ProjectShowProps {
  project: Project;
  stats: ProjectStats;
}

export default function ShowProject({ project, stats }: ProjectShowProps) {
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Projects', href: '/projects' },
    { title: project.name, href: `/projects/${project.id}` },
  ];

  const [activeTab, setActiveTab] = useState<'dashboard' | 'signups' | 'templates'>('dashboard');

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`${project.name} - Dashboard`} />
      <div className="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
          <div className="flex items-center gap-2">
            <h1 className="text-2xl font-bold tracking-tight">{project.name}</h1>
            <Badge variant={project.is_active ? 'default' : 'secondary'}>
              {project.is_active ? 'Active' : 'Inactive'}
            </Badge>
          </div>
          <p className="text-muted-foreground">
            <a
              href={project.full_url}
              target="_blank"
              rel="noopener noreferrer"
              className="hover:underline"
            >
              {project.full_url}
            </a>
          </p>
        </div>
        <div className="flex flex-wrap gap-2">
          <Button variant="outline" size="sm" asChild>
            <Link href={project.full_url} target="_blank" rel="noopener noreferrer">
              View Live Page
            </Link>
          </Button>
          <Button variant="outline" size="sm" asChild>
            <Link href={`/projects/${project.id}/edit`}>
              <PencilLine className="mr-1 size-4" />
              Edit
            </Link>
          </Button>
        </div>
      </div>

      <div className="mb-6">
        <div className="flex space-x-1 overflow-x-auto rounded-lg border bg-card p-1">
          <Button
            variant={activeTab === 'dashboard' ? 'default' : 'ghost'}
            size="sm"
            onClick={() => setActiveTab('dashboard')}
            className="flex-1"
          >
            <BarChart3 className="mr-1 size-4" /> Dashboard
          </Button>
          <Button
            variant={activeTab === 'signups' ? 'default' : 'ghost'}
            size="sm"
            onClick={() => setActiveTab('signups')}
            className="flex-1"
          >
            <Users className="mr-1 size-4" /> Signups
          </Button>
          <Button
            variant={activeTab === 'templates' ? 'default' : 'ghost'}
            size="sm"
            onClick={() => setActiveTab('templates')}
            className="flex-1"
          >
            <Plus className="mr-1 size-4" /> Templates
          </Button>
        </div>
      </div>

      {activeTab === 'dashboard' && <DashboardTab stats={stats} />}
      {activeTab === 'signups' && <SignupsTab signups={project.signups || []} />}
      {activeTab === 'templates' && <TemplatesTab project={project} />}
    </AppLayout>
  );
}

function DashboardTab({ stats }: { stats: ProjectStats }) {
  // Format dates for the chart
  const chartData = Object.entries(stats.daily_signups)
    .map(([date, count]) => ({ date, count }))
    .sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());

  return (
    <div className="space-y-6">
      <div className="grid gap-4 md:grid-cols-3">
        <StatsCard
          title="Total Signups"
          value={stats.total_signups.toString()}
          description="Total number of waitlist signups"
        />
        <StatsCard
          title="Verified Signups"
          value={stats.verified_signups.toString()}
          description="Users who verified their email"
        />
        <StatsCard
          title="Conversion Rate"
          value={`${stats.conversion_rate}%`}
          description="Percentage of verified signups"
        />
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Signups Over Time</CardTitle>
          <CardDescription>Daily signup activity for the last 30 days</CardDescription>
        </CardHeader>
        <CardContent>
          {chartData.length === 0 ? (
            <div className="flex h-64 items-center justify-center">
              <p className="text-center text-muted-foreground">No data available yet</p>
            </div>
          ) : (
            <div className="h-64">
              <div className="relative h-full w-full">
                {/* Simple bar chart representation - In a real app, use a charting library */}
                <div className="absolute inset-0 flex items-end justify-around gap-1 p-4">
                  {chartData.map((item) => {
                    const height = Math.max(
                      (item.count / Math.max(...chartData.map((i) => i.count))) * 100,
                      5
                    );
                    return (
                      <div key={item.date} className="group flex flex-col items-center">
                        <div
                          className="w-6 rounded-t bg-primary transition-all group-hover:opacity-80"
                          style={{ height: `${height}%` }}
                          aria-label={`${item.count} signups on ${new Date(item.date).toLocaleDateString()}`}
                          role="graphics-symbol"
                        />
                        <div className="mt-1 hidden text-xs group-hover:block">
                          {new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}

function SignupsTab({ signups }: { signups: Signup[] }) {
  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h2 className="text-lg font-medium">Waitlist Signups</h2>
        <Button variant="outline" size="sm">
          <Download className="mr-1 size-4" />
          Export CSV
        </Button>
      </div>

      <Card>
        <div className="overflow-hidden rounded-md border">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b bg-muted/50">
                <th className="whitespace-nowrap px-4 py-3 text-left font-medium">#</th>
                <th className="whitespace-nowrap px-4 py-3 text-left font-medium">Email</th>
                <th className="whitespace-nowrap px-4 py-3 text-left font-medium">Name</th>
                <th className="whitespace-nowrap px-4 py-3 text-left font-medium">Status</th>
                <th className="whitespace-nowrap px-4 py-3 text-left font-medium">Date</th>
                <th className="whitespace-nowrap px-4 py-3 text-left font-medium" aria-label="Actions"></th>
              </tr>
            </thead>
            <tbody>
              {signups.length === 0 ? (
                <tr>
                  <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                    No signups yet
                  </td>
                </tr>
              ) : (
                signups.map((signup, index) => (
                  <tr key={signup.id} className="border-b last:border-0 hover:bg-muted/50">
                    <td className="whitespace-nowrap px-4 py-3 tabular-nums text-muted-foreground">{index + 1}</td>
                    <td className="whitespace-nowrap px-4 py-3">{signup.email}</td>
                    <td className="whitespace-nowrap px-4 py-3">{signup.name || '-'}</td>
                    <td className="whitespace-nowrap px-4 py-3">
                      <Badge variant={signup.verified_at ? 'success' : 'secondary'}>
                        {signup.verified_at ? 'Verified' : 'Pending'}
                      </Badge>
                    </td>
                    <td className="whitespace-nowrap px-4 py-3 text-muted-foreground">
                      {new Date(signup.created_at).toLocaleDateString()}
                    </td>
                    <td className="whitespace-nowrap px-4 py-3 text-right">
                      <Button variant="ghost" size="icon" className="h-8 w-8" aria-label="Delete signup">
                        <Trash2 className="size-4 text-muted-foreground" />
                      </Button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}

function TemplatesTab({ project }: { project: Project }) {
  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h2 className="text-lg font-medium">Waitlist Templates</h2>
        <Button asChild>
          <Link href={`/projects/${project.id}/templates`}>
            <Plus className="mr-1 size-4" />
            Add Template
          </Link>
        </Button>
      </div>

      {!project.waitlist_templates || project.waitlist_templates.length === 0 ? (
        <Card>
          <CardContent className="flex flex-col items-center justify-center p-6 text-center">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
              <Plus className="size-6 text-primary" />
            </div>
            <CardTitle className="mb-2">No templates yet</CardTitle>
            <CardDescription className="mb-4">
              Select a template to start collecting waitlist signups.
            </CardDescription>
            <Button asChild>
              <Link href={`/projects/${project.id}/templates`}>Add Template</Link>
            </Button>
          </CardContent>
        </Card>
      ) : (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {project.waitlist_templates.map((template) => (
            <Card key={template.id} className="overflow-hidden">
              <div
                className="h-40 w-full bg-cover bg-center"
                style={{
                  backgroundColor: template.structure.settings.backgroundColor || '#f3f4f6',
                  backgroundImage: template.thumbnail_path ? `url(${template.thumbnail_path})` : undefined,
                }}
                aria-hidden="true"
              />
              <CardHeader className="p-4">
                <div className="flex items-center justify-between">
                  <CardTitle className="line-clamp-1 text-lg">{template.name}</CardTitle>
                  <Badge
                    variant={template.pivot?.is_active ? 'default' : 'secondary'}
                  >
                    {template.pivot?.is_active ? 'Active' : 'Inactive'}
                  </Badge>
                </div>
                <CardDescription className="line-clamp-2">{template.description}</CardDescription>
              </CardHeader>
              <Separator />
              <CardContent className="flex items-center justify-between p-4">
                <Button variant="outline" size="sm" asChild>
                  <Link href={`/projects/${project.id}/templates/${template.id}/edit`}>
                    <PencilLine className="mr-1 size-4" />
                    Customize
                  </Link>
                </Button>
                <Button
                  variant={template.pivot?.is_active ? 'secondary' : 'default'}
                  size="sm"
                >
                  {template.pivot?.is_active ? 'Deactivate' : 'Activate'}
                </Button>
              </CardContent>
            </Card>
          ))}
        </div>
      )}
    </div>
  );
}

function StatsCard({ title, value, description }: { title: string; value: string; description: string }) {
  return (
    <Card>
      <CardHeader className="pb-2">
        <CardTitle className="text-sm font-medium">{title}</CardTitle>
      </CardHeader>
      <CardContent>
        <div className="text-2xl font-bold">{value}</div>
        <p className="text-xs text-muted-foreground">{description}</p>
      </CardContent>
    </Card>
  );
}
