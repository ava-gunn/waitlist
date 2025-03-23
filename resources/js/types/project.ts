import { User } from '.';

export interface Project {
  id: number;
  name: string;
  subdomain: string;
  description: string | null;
  logo_path: string | null;
  settings: ProjectSettings;
  is_active: boolean;
  full_url: string;
  created_at: string;
  updated_at: string;
  user?: User;
  waitlist_templates?: WaitlistTemplate[];
  signups?: Signup[];
  signups_count?: number;
}

export interface ProjectSettings {
  theme?: 'light' | 'dark' | 'auto';
  collect_name?: boolean;
  social_sharing?: boolean;
  [key: string]: unknown;
}

export interface WaitlistTemplate {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  structure: TemplateStructure;
  thumbnail_path: string | null;
  is_active: boolean;
  is_default: boolean;
  created_at: string;
  updated_at: string;
  pivot?: {
    customizations: Record<string, unknown> | null;
    is_active: boolean;
  };
}

export interface TemplateStructure {
  components: TemplateComponent[];
  settings: TemplateSettings;
}

export interface TemplateComponent {
  type: string;
  content?: string;
  level?: number;
  fields?: TemplateField[];
  button?: {
    text: string;
    color: string;
  };
  [key: string]: unknown;
}

export interface TemplateField {
  name: string;
  type: string;
  label: string;
  placeholder?: string;
  required: boolean;
  [key: string]: unknown;
}

export interface TemplateSettings {
  backgroundColor: string;
  textColor: string;
  buttonColor: string;
  buttonTextColor: string;
  [key: string]: unknown;
}

export interface Signup {
  id: number;
  project_id: number;
  email: string;
  name: string | null;
  metadata: Record<string, unknown> | null;
  verified_at: string | null;
  referrer: string | null;
  created_at: string;
  updated_at: string;
  project?: Project;
}

export interface ProjectStats {
  total_signups: number;
  verified_signups: number;
  conversion_rate: number;
  daily_signups: Record<string, number>;
}
