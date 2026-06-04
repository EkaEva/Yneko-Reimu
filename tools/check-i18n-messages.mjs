import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const poPath = resolve(root, 'theme/Yneko-Reimu/languages/en_US.po');

const requiredMessages = [
  '登录信息已过期，请重试。',
  '登录成功。',
  '请输入邮箱和密码。',
  '两步验证码不正确。',
  '请先登录。',
  '权限不足。',
  '个人资料已保存。',
  '个人资料已保存，头像审核中。',
  '个人资料已保存，评论标签审核中。',
  '两次输入的密码不一致。',
  '头像审核中',
  '头像审核不通过',
  '标签审核中',
  '标签审核不通过',
  '评论审核中',
  '评论审核不通过',
  '文件已上传，等待管理员审核。',
  '评论提交失败。',
  '评论已发布。',
  '评论已提交，正在等待审核。',
  '评论已更新。',
  '评论已删除。',
  '上传失败。',
  '当前未开启头像上传。',
  '无效的评论上传附件。',
  '请选择要上传的文件。',
  '文件大小超出限制。',
  '注册成功，请返回登录。',
  '当前未开放注册。',
  '请输入有效的邮箱地址。',
  '该邮箱已被注册。',
  '请输入 6 位邮箱验证码。',
  '验证码已发送，请稍后再试。',
  '验证码已发送，请检查您的邮箱。',
  '验证码邮件发送失败，请稍后重试。',
  '验证码已失效，请重新获取。',
  '验证码错误次数过多，请重新获取。',
  '验证码不正确。',
  '验证码不正确或已失效。',
  '如果该邮箱已注册，验证码将发送到对应邮箱。',
  '请输入注册邮箱。',
  '密码至少需要 8 个字符。',
  '密码已重置，请返回登录。',
  '新邮箱地址不要与原邮箱地址重复。',
  '邮箱验证码不正确或已失效。',
  '认证器验证码不正确。',
  'GitHub 登录成功',
  'GitHub 登录成功，正在返回评论区...',
  'GitHub login is not configured.',
  'Missing GitHub OAuth response.',
  'GitHub login state expired. Please try again.',
  'GitHub did not return an access token.',
  'GitHub API request failed.',
  'GitHub profile is missing required fields.',
  'This GitHub account is already linked to another WordPress account.',
  'No WordPress account is linked to this GitHub account.',
  'This GitHub email already belongs to an existing WordPress account. Please log in normally first, then bind GitHub.'
];

function poUnescape(value) {
  return value
    .replace(/\\n/g, '\n')
    .replace(/\\"/g, '"')
    .replace(/\\\\/g, '\\');
}

function parsePo(source) {
  const messages = new Map();
  const blocks = source.split(/\n\s*\n/);

  for (const block of blocks) {
    const idMatch = block.match(/^msgid\s+"((?:\\.|[^"])*)"/m);
    const strMatch = block.match(/^msgstr\s+"((?:\\.|[^"])*)"/m);
    if (!idMatch || !strMatch) {
      continue;
    }

    messages.set(poUnescape(idMatch[1]), poUnescape(strMatch[1]));
  }

  return messages;
}

const messages = parsePo(await readFile(poPath, 'utf8'));
const failures = [];

for (const msgid of requiredMessages) {
  if (!messages.has(msgid)) {
    failures.push(`Missing msgid in en_US.po: ${msgid}`);
    continue;
  }

  if (!messages.get(msgid).trim()) {
    failures.push(`Empty en_US translation for high-impact message: ${msgid}`);
  }
}

if (failures.length) {
  console.error('[i18n-messages] Contract check failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log(`[i18n-messages] ${requiredMessages.length} high-impact en_US messages are translated.`);
